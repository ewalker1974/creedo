<?php

namespace Creedo\App\Dto;

use Creedo\App\Enum\HttpCode;

readonly class HttpResponse
{
    public function __construct(
        public HttpCode $statusCode,
        /**
         * @var array<string, string>
         */
        public array $headers = [],
        /** @var object|array<string, mixed>|null */
        public object|array|null $body = null
    ) {
    }
}
