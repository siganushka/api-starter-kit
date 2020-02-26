<?php

namespace App\Security\Authenticator;

use App\JWT\JWTManager;
use App\Response\ErrorResponse;
use App\TokenExtractor\TokenExtractorInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JWTAuthenticator extends AbstractGuardAuthenticator
{
    private $viewHandler;
    private $tokenExtractor;
    private $jwtManager;

    public function __construct(ViewHandlerInterface $viewHandler, TokenExtractorInterface $tokenExtractor, JWTManager $jwtManager)
    {
        $this->viewHandler = $viewHandler;
        $this->tokenExtractor = $tokenExtractor;
        $this->jwtManager = $jwtManager;
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
            $jwt = $this->jwtManager->parse($credentials);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        try {
            $user = $userProvider->loadUserByUsername($jwt->getClaim('username'));
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        $response = new ErrorResponse(401, $message);

        $view = View::create($response, $response->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $message = ($authException instanceof AuthenticationException)
            ? strtr($authException->getMessageKey(), $authException->getMessageData())
            : 'Token not found.';

        $response = new ErrorResponse(401, $message);

        $view = View::create($response, $response->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
