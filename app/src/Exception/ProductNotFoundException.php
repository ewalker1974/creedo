<?php

namespace Creedo\App\Exception;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(string $id)
    {
        parent::__construct("Product id: {$id} not found");
    }
}
