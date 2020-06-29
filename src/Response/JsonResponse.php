<?php

namespace TonySchmitt\GestionApi\Response;

use FOS\RestBundle\View\View;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Serializer\ExclusionPolicy("all")
 */
final class JsonResponse
{
    const MESSAGE_DEFAUT = [
        400 => 'Le serveur n\'a pas pu traîter la requête dans cet état. Vérifier la syntaxe.',
        404 => 'Le serveur n\'a pas trouvé la ressource demandée.',
        500 => 'Le serveur a rencontré une situation qu\'il ne sait pas traiter. Veuillez réessayer plus tard.',
    ];

    /**
     * @var string
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $status;
    /**
     * @var array
     * @Serializer\Expose
     */
    private $data;
    /**
     * @var string
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    private $message;

    public function __construct($data = null, int $status = Response::HTTP_OK, string $message = '')
    {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }

    public static function sendSuccess($data = null, string $message = 'Ok.'): View
    {
        return View::create(new static($data, Response::HTTP_OK, $message), Response::HTTP_OK);
    }

    public static function sendCritical(): View
    {
        return View::create(new static(
            null,
            Response::HTTP_INTERNAL_SERVER_ERROR,
            self::MESSAGE_DEFAUT['500']
        ), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function sendError(int $status, string $message): View
    {
        return View::create(new static(
            null,
            $status,
            $message
        ), $status);
    }

    public static function sendResponse(int $status, $data, string $message = ''): View
    {
        return View::create(new static(
            $data,
            $status,
            $message
        ), $status);
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
