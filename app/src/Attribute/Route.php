<?php

namespace Creedo\App\Attribute;

use Attribute;
use Creedo\App\Enum\RequestMethod;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(public RequestMethod $method, public string $path)
    {
    }
}
