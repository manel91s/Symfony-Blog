<?php

namespace App\Controller\Api\Listener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedListener 
{
    public function onJWTDecoded(JWTDecodedEvent $event): array
    {
        $payload = $event->getPayload();

        return $payload;

    }

}