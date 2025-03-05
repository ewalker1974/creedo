<?php

use Creedo\App\Application;
use Creedo\App\Exception\ContainerException;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();
try {
    $app();
} catch (ContainerException $e) {
    throw new RuntimeException('Error in application startup', [], $e);
}


