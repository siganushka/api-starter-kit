<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTTokenListener implements EventSubscriberInterface
{
    private $expiresIn;

    public function __construct(int $expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        $data['access_token'] = $data['token'];
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
