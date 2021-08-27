<?php

namespace App\EventSubscriber;

use App\Exception\AuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class AuthenticationExceptionSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents()
    : array {

        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event) {

        $exception = $event->getThrowable();

        if ($exception instanceof AuthenticationException) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'error' => 'Invalid login credentials provided',
                    ], Response::HTTP_UNAUTHORIZED
                )
            );
        }
    }
}
