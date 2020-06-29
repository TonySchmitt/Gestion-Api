<?php

namespace TonySchmitt\GestionApi\Tests\EventSubscriber;

use Exception;
use FOS\RestBundle\View\ViewHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;
use TonySchmitt\GestionApi\EventSubscriber\ExceptionSubscriber;
use TonySchmitt\GestionApi\Exception\ApiBadRequestException;
use TonySchmitt\GestionApi\Exception\ApiException;
use TonySchmitt\GestionApi\Exception\ApiNotFoundException;
use TonySchmitt\GestionApi\Response\JsonResponse;

class ExceptionSubscriberTest extends TestCase
{
    /**
     * @var ViewHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $viewHandler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private $logger;

    /**
     * @var ExceptionSubscriber
     */
    private $exceptionSubscriber;

    public function setUp(): void
    {
        $this->viewHandler = $this->createMock(ViewHandlerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->exceptionSubscriber = new ExceptionSubscriber(
            $this->viewHandler,
            $this->logger
        );
    }

    public function testGetSubscribedEvents()
    {
        $expected = [
            KernelEvents::EXCEPTION => [
                ['logException', 11],
                ['returnException', 10],
            ],
        ];
        $actual = ExceptionSubscriber::getSubscribedEvents();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider dataLogExceptionProvider
     */
    public function testLogException($exception, $message, $methodLog, $detailLog)
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernel, $request, 1, $exception);

        $this->logger->expects(self::at(0))
            ->method($methodLog)
            ->with(
                $message.' at '.$exception->getFile().' line '.$exception->getLine(),
                $detailLog
            )
        ;

        $this->exceptionSubscriber->logException($event);
    }

    public function dataLogExceptionProvider()
    {
        return [
            'Exception' => [
                'exception' => new Exception('Mon message'),
                'message' => 'Mon message',
                'methodLog' => 'critical',
                'detailLog' =>  [
                    'apiMethod' => null,
                    'apiPath' => null,
                    'contenu' => null
                ]
            ],
            'ApiNotFoundException' => [
                'exception' => new ApiNotFoundException('Non trouvé'),
                'message' => 'Non trouvé',
                'methodLog' => 'warning',
                'detailLog' => [
                    'apiMethod' => null,
                    'apiPath' => null,
                    'contenu' => null
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataReturnExceptionProvider
     */
    public function testReturnException($exception, $code, $message) {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernel, $request, 1, $exception);

        $response = new Response();

        $jsonResponse = JsonResponse::sendError($code, $message);

        $this->viewHandler->expects(self::once())
            ->method('handle')
            ->with($jsonResponse)
            ->willReturn($response)
        ;

        $this->exceptionSubscriber->returnException($event);
    }

    public function dataReturnExceptionProvider()
    {
        return [
            'Exception' => [
                'exception' => new Exception('Mon message'),
                'code' => 500,
                'message' => JsonResponse::MESSAGE_DEFAUT[500],
            ],
            'ApiNotFoundException' => [
                'exception' => new ApiNotFoundException('Non trouvé'),
                'code' => 404,
                'message' => JsonResponse::MESSAGE_DEFAUT[404],
            ],
            'ApiBadRequestException' => [
                'exception' => new ApiBadRequestException('Erreur 500', 'Message personnalisé'),
                'code' => 400,
                'message' => 'Message personnalisé',
            ],
            'ApiTestException' => [
                'exception' => new ApiTestException('Message d\'erreur', null, 422),
                'code' => 422,
                'message' => JsonResponse::MESSAGE_DEFAUT[500],
            ],
        ];
    }
}

class ApiTestException extends ApiException
{
    public function __construct(string $message = "", string $messageRetour = null, int $code, Throwable $previous = null)
    {
        parent::__construct($code, $message, $messageRetour, $previous);
    }
}