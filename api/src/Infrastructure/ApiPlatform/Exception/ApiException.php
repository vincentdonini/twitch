<?php

namespace App\Infrastructure\ApiPlatform\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    private string $errorKey;

    public function __construct(
        string  $errorKey,
        int     $statusCode = 400,
        ?string $message = null
    ) {
        $this->errorKey = $errorKey;
        $this->message  = $message;
        parent::__construct($statusCode);
    }

    public function getErrorKey(): string
    {
        return $this->errorKey;
    }

    public function getCustomMessage(): ?string
    {
        return $this->message;
    }
}
