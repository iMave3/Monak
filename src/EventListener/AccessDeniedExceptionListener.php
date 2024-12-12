<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedExceptionListener
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            $event->getRequest()->getSession()->getFlashBag()->add('error', 'Přístup odepřen!');

            // Redirect to main page
            $url = $this->urlGenerator->generate('main');
            $response = new RedirectResponse($url);
            
            // Set the response
            $event->setResponse($response);
        }
    }
}
