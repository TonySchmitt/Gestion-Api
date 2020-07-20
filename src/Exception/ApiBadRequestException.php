<?php

namespace TonySchmitt\GestionApi\Exception;

use Throwable;

class ApiBadRequestException extends ApiException
{
    public function __construct(
        string $message = "",
        string $messageRetour = null,
        int $code = 400,
        Throwable $previous = null
    ) {
        parent::__construct($code, $message, $messageRetour, $previous);
    }
}
