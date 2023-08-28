<?php

namespace App\Controller\Api\Listener;

class JWTCreatedListener 
{
    public function onJWTCreated($event) :void
    {
        $user = $event->getUser();

        $payload = $event->getData();
        $payload['userId'] = $user->getId();
        $event->setData($payload);
    }
}