<?php

namespace App\Security\Authenticator;

use App\Error\Error;
use App\JWT\TokenManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;

class UsernamePasswordAuthenticator extends AbstractGuardAuthenticator
{
    private $httpUtils;
    private $passwordEncoder;
    private $viewHandler;
    private $tokenManager;
    private $options;

    public function __construct(HttpUtils $httpUtils, UserPasswordEncoderInterface $passwordEncoder, ViewHandlerInterface $viewHandler, TokenManager $tokenManager, array $options = [])
    {
        $this->httpUtils = $httpUtils;
        $this->passwordEncoder = $passwordEncoder;
        $this->viewHandler = $viewHandler;
        $this->tokenManager = $tokenManager;
        $this->options = array_merge([
            'username_path' => 'username',
            'password_path' => 'password',
            'check_path' => 'api_token',
        ], $options);
    }

    public function supports(Request $request)
    {
        return $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
    }

    public function getCredentials(Request $request)
    {
        $username = ParameterBagUtils::getParameterBagValue($request->request, $this->options['username_path']);
        $password = ParameterBagUtils::getParameterBagValue($request->request, $this->options['password_path']);

        if (null === $username) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" can not be empty.', $this->options['username_path']));
        }

        if (null === $password) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" can not be empty.', $this->options['password_path']));
        }

        if (!\is_string($username)) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" must be a string.', $this->options['username_path']));
        }

        if (!\is_string($password)) {
            throw new CustomUserMessageAuthenticationException(sprintf('The "%s" must be a string.', $this->options['password_path']));
        }

        return compact('username', 'password');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $error = new Error(Response::HTTP_UNAUTHORIZED, $exception->getMessageKey());

        $view = View::create($error, $error->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $token = $this->tokenManager->create($token->getUser());

        $view = View::create($token);

        return $this->viewHandler->handle($view);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $message = ($authException instanceof AuthenticationException)
            ? strtr($authException->getMessageKey(), $authException->getMessageData())
            : 'Login Required.';

        $error = new Error(Response::HTTP_UNAUTHORIZED, $message);

        $view = View::create($error, $error->getStatus());

        return $this->viewHandler->handle($view);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
