<?php

namespace App\Security\Authenticator;

use App\Exception\APIException;
use App\JWT\RefreshTokenManager;
use App\JWT\TokenManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;

class RefreshTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $httpUtils;
    private $viewHandler;
    private $tokenManager;
    private $refreshTokenManager;
    private $options;

    public function __construct(HttpUtils $httpUtils, ViewHandlerInterface $viewHandler, TokenManager $tokenManager, RefreshTokenManager $refreshTokenManager, array $options = [])
    {
        $this->httpUtils = $httpUtils;
        $this->viewHandler = $viewHandler;
        $this->tokenManager = $tokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->options = array_merge([
            'refresh_token' => 'refreshToken',
            'check_path' => 'api_token_put',
        ], $options);
    }

    public function supports(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
    }

    public function getCredentials(Request $request)
    {
        $refreshToken = ParameterBagUtils::getParameterBagValue($request->request, $this->options['refresh_token']);

        if (null === $refreshToken) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" can not be empty.', $this->options['refresh_token']));
        }

        if (!\is_string($refreshToken)) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" must be a string.', $this->options['refresh_token']));
        }

        return $refreshToken;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $user = $this->refreshTokenManager->loadUserByRefreshToken($credentials);
        } catch (\Throwable $th) {
            throw new CustomUserMessageAuthenticationException($th->getMessage());
        }

        if ($user->isRefreshTokenExpired()) {
            throw new CustomUserMessageAuthenticationException('Expired refresh Token.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new APIException(401, $exception->getMessageKey(), $exception->getMessageData(), 'security');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $token = $this->tokenManager->create($token->getUser());

        $view = View::create($token);

        return $this->viewHandler->handle($view);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        if (!$authException instanceof AuthenticationException) {
            $authException = new TokenNotFoundException();
        }

        throw new APIException(401, $authException->getMessageKey(), $authException->getMessageData(), 'security');
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
