<?php


namespace TonySchmitt\GestionApi\Annotations;

use Swagger\Annotations\Items;
use Swagger\Annotations\Property;
use Swagger\Annotations\Response;
use Swagger\Annotations\Schema;

/**
 * @Annotation
 */
class ResponseModel extends Response
{
    public function __construct($properties)
    {
        // data
        $dataArray = [
            'property' => 'data',
        ];

        // Exemple data="object"
        if (!empty($properties['data'])) {
            $dataArray['type'] = $properties['data'];
        }

        // Exemple : dataRef=@Model(type=JsonResponse::class),
        if (!empty($properties['dataRef'])) {
            $dataArray['ref'] = $properties['dataRef'];
        }

        $data = new Property($dataArray);

        // status
        $status = new Property([
            'property' => 'status',
            'type' => 'integer',
        ]);

        // messages
        $itemMessages = new Items(['type' => 'string']);
        $messages = new Property([
            'property' => 'messages',
            'type' => 'array',
            'value' => $itemMessages,
        ]);

        // errors
        $itemErrors = new Items(['type' => 'string']);
        $errors = new Property([
            'property' => 'errors',
            'type' => 'array',
            'value' => $itemErrors,
        ]);
        $schema = new Schema(['type' => 'object', 'value' => [
            'data' => $data,
            'status' => $status,
            'messages' => $messages,
            'errors' => $errors,
        ]]);

        parent::__construct([
            'response' => $properties['response'],
            'description' => $properties['description'],
            'value' => $schema,
        ]);
    }
}
