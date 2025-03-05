<?php

namespace Creedo\App\Http;

use Closure;
use Creedo\App\Dto\HttpResponse;
use Creedo\App\Enum\RequestMethod;

readonly class ActionHandler
{
    public function __construct(public RequestMethod $method, public string $route, private Closure $action)
    {
    }

    /**
     * @param array<string, mixed> $params
     */
    public function handle(array $params): HttpResponse
    {
        return ($this->action)($params);
    }
}
