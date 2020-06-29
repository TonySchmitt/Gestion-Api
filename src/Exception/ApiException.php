<?php

namespace TonySchmitt\GestionApi\Exception;

use Throwable;

class ApiException extends \Exception
{
    /**
     * @var string|null
     */
    private $messageRetour;

    public function __construct(int $code = 0, string $message = "", ?string $messageRetour = null, Throwable $previous = null)
    {
        $this->messageRetour = $messageRetour;
        parent::__construct($message, $code, $previous);
    }

    public function getMessageRetour(): ?string
    {
        return $this->messageRetour;
    }
}