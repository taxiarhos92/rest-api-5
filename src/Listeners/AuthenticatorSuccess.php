<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticatorSuccess {

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event){
        /*$response = $event->getResponse();
        $data = $event->getData();

        var_dump($data);*/
    }
}