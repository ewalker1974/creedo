<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';

    function preload($dir): void
    {
        foreach (scandir($dir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = "$dir/$file";

            if (is_dir($path) && $file !== 'Attribute') {
                preload($path);
            } elseif (str_ends_with($file, '.php')) {
                opcache_compile_file($path);
            }
        }
    }

    preload(__DIR__ . '/src');
}
