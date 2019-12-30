<?php

namespace App\Security\Authenticator;

use App\JWT\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    private $tokenExtractor;
    private $refreshTokenManager;
    private $jwtTokenManager;

    public function __construct(
        UserCheckerInterface $userChecker,
        TokenExtractorInterface $tokenExtractor,
        RefreshTokenManager $refreshTokenManager,
        JWTTokenManagerInterface $jwtTokenManager)
    {
        $this->userChecker = $userChecker;
        $this->tokenExtractor = $tokenExtractor;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->jwtTokenManager = $jwtTokenManager;
    }

    public function supports(Request $request)
    {
        return $this->tokenExtractor->extract($request);
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
            throw new CustomUserMessageAuthenticationException(sprintf('Invalid refresh token "%s"', $credentials));
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
        $data = [
            'message' => $exception->getMessage(),
        ];

        return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser()->getUser();

        $token = $this->refreshTokenManager->update($user);
        $jwtAsString = $this->jwtTokenManager->create($user);

        $data = [
            'access_token' => $jwtAsString,
            'refresh_token' => $token->getRefreshToken(),
            'expires_in' => 3600,
        ];

        return new JsonResponse($data);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
