<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    public function start(
        Request                 $request,
        AuthenticationException $authException = null
    ) {

        if (!is_null($authException)) {
            return new JsonResponse(
                ['error' => 'Authentication required to complete this request'],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}