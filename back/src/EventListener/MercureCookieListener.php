<?php

namespace App\EventListener;

use App\Service\MercureJwtGenerator;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Cookie;


class MercureCookieListener
{
    private Security $security;
    private MercureJwtGenerator $jwtGenerator;

    public function __construct(Security $security, MercureJwtGenerator $jwtGenerator)
    {
        $this->security = $security;
        $this->jwtGenerator = $jwtGenerator;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $jwt = $this->jwtGenerator->createJwt($user);

        $cookie = Cookie::create(
            'mercureAuthorization',
            $jwt,
            time() + 3600,
            '/',
            null,
            false, 
            true,
            false,
            Cookie::SAMESITE_STRICT
        );

        $event->getResponse()->headers->setCookie($cookie);
    }
}
