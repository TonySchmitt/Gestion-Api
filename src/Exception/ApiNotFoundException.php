<?php

namespace TonySchmitt\GestionApi\Exception;

use Throwable;

class ApiNotFoundException extends ApiException
{
    public function __construct(
        string $message = "",
        string $messageRetour = null,
        int $code = 404,
        Throwable $previous = null
    ) {
        parent::__construct($code, $message, $messageRetour, $previous);
    }
}
