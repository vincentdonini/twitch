<?php

namespace App\Infrastructure\Paginator;

use InvalidArgumentException;
use Throwable;

final class InvalidPaginationArgumentException extends InvalidArgumentException
{
    public function __construct($message = "", $code = 400, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
