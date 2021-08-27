<?php

namespace App\EventSubscriber;

use App\Exception\ParameterNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ParameterNotFoundExceptionSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents()
    : array {

        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event) {

        $exception = $event->getThrowable();
        if ($exception instanceof ParameterNotFoundException) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'error' => $exception->getMessage(),
                    ], Response::HTTP_BAD_REQUEST
                )
            );
        }
    }
}
