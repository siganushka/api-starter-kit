<?php

namespace App\Security\Authenticator;

use App\Error\Error;
use App\JWT\AccessTokenManager;
use App\JWT\RefreshTokenManager;
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
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;

class RefreshTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $httpUtils;
    private $viewHandler;
    private $refreshTokenManager;
    private $accessTokenManager;
    private $options;

    public function __construct(HttpUtils $httpUtils, ViewHandlerInterface $viewHandler, RefreshTokenManager $refreshTokenManager, AccessTokenManager $accessTokenManager, array $options = [])
    {
        $this->httpUtils = $httpUtils;
        $this->viewHandler = $viewHandler;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->accessTokenManager = $accessTokenManager;
        $this->options = array_merge([
            'refresh_token' => 'refresh_token',
            'check_path' => 'api_refresh_token',
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
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" can not be empty', $this->options['refresh_token']));
        }

        if (!\is_string($refreshToken)) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" must be a string', $this->options['refresh_token']));
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
            throw new CustomUserMessageAuthenticationException('Expired refresh Token');
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
        $accessToken = $this->accessTokenManager->create($token->getUser());

        $view = View::create($accessToken);

        return $this->viewHandler->handle($view);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $error = new Error(Response::HTTP_UNAUTHORIZED, 'Refresh Token not found');

        $view = View::create($error, $error->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
