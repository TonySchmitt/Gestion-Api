<?php

namespace TonySchmitt\GestionApi\Tests\Response;

use PHPUnit\Framework\TestCase;
use TonySchmitt\GestionApi\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseTest extends TestCase
{
    /**
     * @dataProvider sendSuccessProvider
     */
    public function testSendSuccess($data, string $message)
    {
        $status = Response::HTTP_OK;

        $actual = JsonResponse::sendSuccess($data, $message, $status);

        $this->assertEquals(
            $status,
            $actual->getStatusCode()
        );

        $this->assertEquals(
            $status,
            $actual->getData()->getStatus()
        );


        $this->assertEquals(
            $data,
            $actual->getData()->getData()
        );

        $this->assertEquals(
            $message,
            $actual->getData()->getMessage()
        );
    }

    public function sendSuccessProvider()
    {
        return [
            'Reponse avec datas et un message' => [
                'data' => ['myData' => 'myValue'],
                'message' => 'Je vois mes données',
            ],
            'Reponse sans data, avec plusieurs messages' => [
                'data' => [],
                'message' => 'Je vois mon premier message',
            ],
        ];
    }

    public function testSendCritical()
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;

        $actual = JsonResponse::sendCritical();

        $this->assertEquals(
            $status,
            $actual->getStatusCode()
        );


        $this->assertEquals(
            $status,
            $actual->getData()->getStatus()
        );


        $this->assertEquals(
            null,
            $actual->getData()->getData()
        );

        $this->assertEquals(
            JsonResponse::MESSAGE_DEFAUT[500],
            $actual->getData()->getMessage()
        );
    }

    public function testSendError()
    {
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'Erreur.';

        $actual = JsonResponse::sendError($status, $message);

        $this->assertEquals(
            $status,
            $actual->getStatusCode()
        );


        $this->assertEquals(
            $status,
            $actual->getData()->getStatus()
        );


        $this->assertEquals(
            null,
            $actual->getData()->getData()
        );

        $this->assertEquals(
            $message,
            $actual->getData()->getMessage()
        );
    }


    /**
     * @dataProvider sendResponseProvider
     */
    public function testSendResponse($data, string $message, $status)
    {
        $actual = JsonResponse::sendResponse($status, $data, $message);

        $this->assertEquals(
            $status,
            $actual->getStatusCode()
        );

        $this->assertEquals(
            $status,
            $actual->getData()->getStatus()
        );


        $this->assertEquals(
            $data,
            $actual->getData()->getData()
        );

        $this->assertEquals(
            $message,
            $actual->getData()->getMessage()
        );
    }

    public function sendResponseProvider()
    {
        return [
            'Reponse avec datas et un message' => [
                'data' => ['myData' => 'myValue'],
                'message' => 'Je vois mes données',
                'status' => '422'
            ],
            'Reponse sans data, avec plusieurs messages' => [
                'data' => [],
                'message' => 'Je vois mon premier message',
                'status' => '404'
            ],
        ];
    }
}