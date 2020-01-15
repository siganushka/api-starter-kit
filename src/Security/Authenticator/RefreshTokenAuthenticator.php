<?php

namespace App\Security\Authenticator;

use App\JWT\AccessTokenManager;
use App\JWT\RefreshTokenManager;
use App\TokenExtractor\TokenExtractorInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class RefreshTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $viewHandler;
    private $tokenExtractor;
    private $refreshTokenManager;
    private $accessTokenManager;

    public function __construct(ViewHandlerInterface $viewHandler, TokenExtractorInterface $tokenExtractor, RefreshTokenManager $refreshTokenManager, AccessTokenManager $accessTokenManager)
    {
        $this->viewHandler = $viewHandler;
        $this->tokenExtractor = $tokenExtractor;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->accessTokenManager = $accessTokenManager;
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
            $user = $this->refreshTokenManager->loadUserByRefreshToken($credentials);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException($th->getMessage());
        }

        if ($user->isRefreshTokenExpired()) {
            throw new CustomUserMessageAuthenticationException('Expired Refresh Token');
        }

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
        $accessToken = $this->accessTokenManager->create($token->getUser());

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
