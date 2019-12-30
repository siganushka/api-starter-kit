<?php

namespace App\EventListener;

use App\JWT\RefreshTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RefreshTokenListener implements EventSubscriberInterface
{
    private $refreshTokenManager;
    private $expiresIn;

    public function __construct(RefreshTokenManager $refreshTokenManager, int $expiresIn)
    {
        $this->refreshTokenManager = $refreshTokenManager;
        $this->expiresIn = $expiresIn;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        try {
            $token = $this->refreshTokenManager->update($event->getUser());
        } catch (\Throwable $th) {
            // do stuff...
            throw $th;
        }

        $data = $event->getData();
        $data['access_token'] = $data['token'];
        $data['refresh_token'] = $token->getRefreshToken();
        $data['expires_in'] = $this->expiresIn;

        // remove origin key
        unset($data['token']);

        $event->setData($data);
    }

    public static function getSubscribedEvents()
    {
        return [Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess'];
    }
}
