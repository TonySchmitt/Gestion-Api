<?php

namespace TonySchmitt\GestionApi\EventSubscriber;

use FOS\RestBundle\View\ViewHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TonySchmitt\GestionApi\Exception\ApiException;
use TonySchmitt\GestionApi\Response\JsonResponse;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ViewHandlerInterface $viewHandler, LoggerInterface $logger)
    {
        $this->viewHandler = $viewHandler;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['logException', 11],
                ['returnException', 10],
            ],
        ];
    }

    public function logException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $detailLog = [];

        if ($request = $event->getRequest()) {
            $detailLog = [
                'apiMethod' => $request->getMethod(),
                'apiPath' => $request->getPathInfo(),
                'contenu' => $request->getContent(),
            ];
        }

        switch (get_class($exception)) {
            case "TonySchmitt\GestionApi\Exception\ApiBadRequestException":
            case "TonySchmitt\GestionApi\Exception\ApiNotFoundException":
                $loggerLevel = 'warning';
                break;
            default:
                $loggerLevel = 'critical';
        }

        $this->logger->$loggerLevel(
            $exception->getMessage().' at '.$exception->getFile().' line '.$exception->getLine(),
            $detailLog
        );
        $this->logger->debug($exception->getTraceAsString());
    }

    public function returnException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $code = $exception->getCode();

        if (is_subclass_of($exception, ApiException::class)) {
            if ($exception->getMessageRetour()) {
                $message = $exception->getMessageRetour();
            } elseif (array_key_exists($code, JsonResponse::MESSAGE_DEFAUT)) {
                $message = JsonResponse::MESSAGE_DEFAUT[$code];
            } else {
                $message = JsonResponse::MESSAGE_DEFAUT[500];
            }
            $json = JsonResponse::sendError($exception->getCode(), $message);
        } else {
            $json = JsonResponse::sendError(
                500,
                JsonResponse::MESSAGE_DEFAUT[500]
            );
        }

        $event->setResponse($this->viewHandler->handle($json));
    }
}