<?php

namespace App\Security\Authenticator;

use App\JWT\JWTManager;
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

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $userChecker;
    private $viewHandler;
    private $tokenExtractor;
    private $JWTManager;

    public function __construct(
        UserCheckerInterface $userChecker,
        ViewHandlerInterface $viewHandler,
        TokenExtractorInterface $tokenExtractor,
        JWTManager $JWTManager)
    {
        $this->userChecker = $userChecker;
        $this->viewHandler = $viewHandler;
        $this->tokenExtractor = $tokenExtractor;
        $this->JWTManager = $JWTManager;
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
            $token = $this->JWTManager->decode($credentials);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException('asdf');
        }

        try {
            $user = $userProvider->loadUserByUsername($token->getClaim('username'));
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException("Invalid jwt token: {$credentials}");
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
        throw $exception;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $view = View::create([
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => 'JWT Token not found.',
        ]);

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
