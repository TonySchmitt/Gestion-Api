<?php


namespace TonySchmitt\GestionApi\Tests\Annotations;

use PHPUnit\Framework\TestCase;
use Swagger\Annotations\Schema;
use TonySchmitt\GestionApi\Annotations\ResponseModel;

class ResponseModelTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testSuccess($properties)
    {
        $responseModel = new ResponseModel($properties);

        $this->assertEquals($properties['response'], $responseModel->response);
        $this->assertEquals($properties['description'], $responseModel->description);
        $this->assertInstanceOf(Schema::class, $responseModel->schema);
    }

    public function dataProvider()
    {
        return [
            'avec Data' => [
                'properties' => [
                    'response' => 200,
                    'description' => 'Ma belle description',
                    'data' => 'object',
                ],
            ],
            'avec dataRef' => [
                'properties' => [
                    'response' => 200,
                    'description' => 'Ma belle description',
                    'dataRef' => 'object',
                ],
            ],
        ];
    }
}
