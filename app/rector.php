<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/test',
    ])
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPreparedSets(deadCode: true, codeQuality: true)
    ->withTypeCoverageLevel(0);

