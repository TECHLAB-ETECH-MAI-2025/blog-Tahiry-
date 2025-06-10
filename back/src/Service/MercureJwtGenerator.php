<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\User\UserInterface;

class MercureJwtGenerator
{
    private string $secret;

    public function __construct(string $mercureSecret)
    {
        $this->secret = $mercureSecret;
    }

    public function createJwt(UserInterface $user): string
    {
        
        if (!method_exists($user, 'getId')) {
            throw new \InvalidArgumentException('User object must have a getId() method.');
        }

        $payload = [
            'mercure' => [
                'subscribe' => [sprintf('http://localhost:3000/chat/%d', $user->getId())],
                'publish' => ['*'],
            ],
            'exp' => time() + 3600,
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }
}
