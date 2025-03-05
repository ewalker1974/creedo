<?php

namespace Creedo\App\Dto;

use Creedo\App\Enum\HttpCode;

readonly class ErrorResponse
{
    public function __construct(
        public string $message,
        public string $statusCode,
    ) {
    }


}
