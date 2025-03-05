<?php

namespace Creedo\App\Http;

readonly class RequestContext
{
    public function __construct(
        public ActionHandler $handler,
        /** @var array <string, mixed> */
        public array $params
    ) {
    }
}
