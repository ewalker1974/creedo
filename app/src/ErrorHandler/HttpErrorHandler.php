<?php

namespace Creedo\App\ErrorHandler;

use Creedo\App\Dto\ErrorResponse;
use Creedo\App\Dto\HttpResponse;
use Creedo\App\Enum\HttpCode;
use Creedo\App\Exception\HttpException;
use Creedo\App\Exception\ProductNotFoundException;
use Psr\Log\LoggerInterface;
use RMValidator\Exceptions\Base\IValidationException;
use Throwable;

class HttpErrorHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function handleException(Throwable $exception): HttpResponse
    {
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);

        $code = HttpCode::HTTP_INTERNAL_SERVER_ERROR;
        $message = $exception->getMessage();
        if ($exception instanceof HttpException) {
            $code = HttpCode::from($exception->getCode());
        } elseif ($exception instanceof ProductNotFoundException) {
            $code = HttpCode::HTTP_NOT_FOUND;
        } elseif ($exception instanceof IValidationException) {
            $code = HttpCode::HTTP_BAD_REQUEST;
            $message = $exception->getOrigMsg();
        }
        $response = new ErrorResponse($message, $this->codeToMessage($code));

        return new HttpResponse($code, [], $response);
    }

    private function codeToMessage(HttpCode $code): string
    {
        return match ($code) {
            HttpCode::HTTP_NOT_FOUND => 'Not Found',
            HttpCode::HTTP_BAD_REQUEST => 'Input parameters are invalid',
            HttpCode::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
            default => 'Unknown Error',
        };
    }
}
