<?php

namespace Creedo\App\DependencyInjection;

use Creedo\App\Exception\ContainerException;

final class Container
{
    private static ?Container $instance = null;
    /** @var array<string, object>  */
    private array $services = [];
    private function __construct()
    {
    }

    public static function getInstance(): Container
    {
        if (!self::$instance instanceof Container) {
            self::$instance = new Container();
        }

        return self::$instance;
    }

    public function register(string $id, object $service): void
    {
        $this->services[$id] = $service;
    }

    /**
     * @throws ContainerException
     */
    public function get(string $id): object
    {
        if (!isset($this->services[$id])) {
            throw new ContainerException("Service with ID {$id} not found");
        }

        return $this->services[$id];
    }
}
