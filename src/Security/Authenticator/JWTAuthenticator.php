<?php

namespace App\Security\Authenticator;

use App\Exception\APIException;
use App\JWT\Exception\JWTExpiredException;
use App\JWT\JWTManager;
use App\Security\Extractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JWTAuthenticator extends AbstractGuardAuthenticator
{
    private $tokenExtractor;
    private $jwtManager;

    public function __construct(TokenExtractorInterface $tokenExtractor, JWTManager $jwtManager)
    {
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
        } catch (JWTExpiredException $th) {
            throw new CredentialsExpiredException();
        } catch (\Throwable $th) {
            throw new BadCredentialsException();
        }

        return $userProvider->loadUserByUsername($jwt->getClaim('username'));
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
        return;
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
