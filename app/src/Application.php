<?php

namespace Creedo\App;

use Creedo\App\Bootstrap\AppBoot;
use Creedo\App\DependencyInjection\Container;
use Creedo\App\Exception\ContainerException;
use Creedo\App\Http\RequestHandler;

class Application
{
    /**
     * @throws ContainerException
     */
    public function __invoke(): void
    {
        $boot = new AppBoot();
        $boot();

        $container = Container::getInstance();
        /** @var RequestHandler $requestHandler */
        $requestHandler = $container->get(RequestHandler::class);
        $requestHandler->handle();
    }
}
