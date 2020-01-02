<?php

namespace App\Security\Authenticator;

use App\JWT\AccessToken;
use App\JWT\JWTManager;
use App\JWT\RefreshTokenManager;
use App\TokenExtractor\TokenExtractorInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class RefreshTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $userChecker;
    private $refreshTokenManager;
    private $jwtManager;
    private $tokenExtractor;
    private $viewHandler;
    private $ttl;

    public function __construct(
        UserCheckerInterface $userChecker,
        RefreshTokenManager $refreshTokenManager,
        JWTManager $jwtManager,
        TokenExtractorInterface $tokenExtractor,
        ViewHandlerInterface $viewHandler,
        int $ttl)
    {
        $this->userChecker = $userChecker;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->jwtManager = $jwtManager;
        $this->tokenExtractor = $tokenExtractor;
        $this->viewHandler = $viewHandler;
        $this->ttl = $ttl;
    }

    public function supports(Request $request)
    {
        return null !== $this->tokenExtractor->extract($request);
    }

    public function getCredentials(Request $request)
    {
        return $this->tokenExtractor->extract($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $user = $userProvider->loadUserByUsername($credentials);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException("Invalid refresh token: {$credentials}");
        }

        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $view = View::create([
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ]);

        return $this->viewHandler->handle($view);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser()->getUser();

        $new = $this->refreshTokenManager->update($user);
        $jwt = (string) $this->jwtManager->encode($user);

        $accessToken = new AccessToken($jwt, $new->getRefreshToken(), $this->ttl);
        $view = View::create($accessToken);

        return $this->viewHandler->handle($view);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $view = View::create([
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Refresh Token not found.',
        ]);

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
