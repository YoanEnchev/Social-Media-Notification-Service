<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidateBearerToken implements EventSubscriberInterface
{
    public function onKernelController(ControllerEvent $event)
    {
        // Apply for all endpoints.

        $bearerToken = $event->getRequest()->headers->get('Authorization');
    
        if ($bearerToken !== 'Bearer ' . $event->getRequest()->server->get('MICROSERVICE_SECRET')) {
            throw new AccessDeniedHttpException('Invalid bearer token.');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}