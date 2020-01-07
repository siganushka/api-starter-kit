<?php

namespace App\Security\Authenticator;

use App\JWT\AccessToken;
use App\JWT\JWTManager;
use App\JWT\RefreshTokenManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\ParameterBagUtils;

class AccessTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $httpUtils;
    private $passwordEncoder;
    private $viewHandler;
    private $jwtManager;
    private $refreshTokenManager;
    private $options;
    private $ttl;

    public function __construct(
        HttpUtils $httpUtils,
        UserPasswordEncoderInterface $passwordEncoder,
        ViewHandlerInterface $viewHandler,
        JWTManager $jwtManager,
        RefreshTokenManager $refreshTokenManager,
        array $options = [],
        int $ttl)
    {
        $this->httpUtils = $httpUtils;
        $this->passwordEncoder = $passwordEncoder;
        $this->viewHandler = $viewHandler;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->options = array_merge([
            'username_path' => 'username',
            'password_path' => 'password',
            'check_path' => 'api_access_token',
        ], $options);
        $this->ttl = $ttl;
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

        if (\strlen($username) > Security::MAX_USERNAME_LENGTH) {
            throw new CustomUserMessageAuthenticationException('Invalid username.');
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
        $view = View::create([
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ]);

        return $this->viewHandler->handle($view);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $refreshToken = $this->refreshTokenManager->update($token->getUser());
        $jwt = (string) $this->jwtManager->create($token->getUser());

        $accessToken = new AccessToken($jwt, $refreshToken, $this->ttl);
        $accessToken->setAccessToken($jwt);
        $accessToken->setExpiresIn($this->ttl);
        $accessToken->setRefreshToken($refreshToken);

        $view = View::create($accessToken);

        return $this->viewHandler->handle($view);
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
