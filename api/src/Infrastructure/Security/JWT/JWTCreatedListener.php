<?php

namespace App\Infrastructure\Security\JWT;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['id']         = $user->getId();
        $payload['email']      = $user->getEmail();
        $payload['first_name'] = $user->getFirstName();
        $payload['last_name']  = $user->getLastName();

        // ROLES
        $payload['roles'] = $user->getRoles();

        // PERMISSIONS
        $payload['permissions'] = $user->getPermissions();

        $event->setData($payload);
    }
}
