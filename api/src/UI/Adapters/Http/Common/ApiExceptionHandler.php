<?php

namespace App\UI\Adapters\Http\Common;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Core\Exceptions\InvalidPayloadException;
use DomainException;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait ApiExceptionHandler
{
    protected function handleException(\Throwable $e): JsonResponse
    {
        $isDev = ($_SERVER['APP_ENV'] ?? 'prod') === 'dev';

        $httpStatusMap = [
            InvalidPayloadException::class  => Response::HTTP_BAD_REQUEST,
            InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
            DomainException::class          => Response::HTTP_UNPROCESSABLE_ENTITY,
            LogicException::class           => Response::HTTP_BAD_REQUEST,
            EntityNotFoundException::class  => Response::HTTP_NOT_FOUND,
            AlreadyExistException::class    => Response::HTTP_CONFLICT,
            AccessDeniedException::class    => Response::HTTP_FORBIDDEN,
        ];

        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        foreach ($httpStatusMap as $class => $httpCode) {
            if ($e instanceof $class) {
                $status = $httpCode;
                break;
            }
        }

        $body = $isDev ? ['error' => $e->getMessage()] : null;

        return new JsonResponse($body, $status);
    }
}
