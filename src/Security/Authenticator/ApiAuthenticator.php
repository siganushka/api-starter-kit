<?php

namespace App\Security\Authenticator;

use App\Error\Error;
use App\JWT\JWTManager;
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

class ApiAuthenticator extends AbstractGuardAuthenticator
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
            throw new CustomUserMessageAuthenticationException($th->getMessage());
        }

        try {
            $user = $userProvider->loadUserByUsername($jwt->getClaim('username'));
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $error = new Error(Response::HTTP_UNAUTHORIZED, $exception->getMessageKey());

        $view = View::create($error, $error->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $error = new Error(Response::HTTP_UNAUTHORIZED, 'Token not found.');

        $view = View::create($error, $error->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
