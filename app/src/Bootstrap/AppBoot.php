<?php

namespace Creedo\App\Bootstrap;

use Creedo\App\Controller\ProductController;
use Creedo\App\Db\MongoDBConnection;
use Creedo\App\DependencyInjection\Container;
use Creedo\App\ErrorHandler\HttpErrorHandler;
use Creedo\App\Exception\ContainerException;
use Creedo\App\Http\JsonSender;
use Creedo\App\Http\RequestHandler;
use Creedo\App\Http\Router;
use Creedo\App\Repository\CrudProductRepository;
use Creedo\App\Repository\MongoCrudProductRepository;
use Creedo\App\Service\ProductService;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class AppBoot
{
    /**
     * @throws ContainerException
     */
    public function __invoke(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '../../../');
        $dotenv->load();

        $container = Container::getInstance();

        $log = new Logger('app');
        $log->pushHandler(new StreamHandler(__DIR__ . '../../../var/log/app.log', Level::Debug));

        $container->register(LoggerInterface::class, $log);
        $container->register(MongoDBConnection::class, new MongoDBConnection());
        $container->register(
            CrudProductRepository::class,
            /** @phpstan-ignore argument.type */
            new MongoCrudProductRepository($container->get(MongoDBConnection::class))
        );
        /** @phpstan-ignore argument.type */
        $container->register(ProductService::class, new ProductService($container->get(CrudProductRepository::class)));
        /** @phpstan-ignore argument.type */
        $container->register(HttpErrorHandler::class, new HttpErrorHandler($container->get(LoggerInterface::class)));
        $container->register(JsonSender::class, new JsonSender());
        $router = new Router();
        $router->addController(new ProductController(
            /** @phpstan-ignore argument.type */
            $container->get(ProductService::class),
            /** @phpstan-ignore argument.type */
            $container->get(LoggerInterface::class)
        ));
        $container->register(Router::class, $router);
        $container->register(RequestHandler::class, new RequestHandler(
            /** @phpstan-ignore argument.type */
            $container->get(Router::class),
            /** @phpstan-ignore argument.type */
            $container->get(HttpErrorHandler::class),
            /** @phpstan-ignore argument.type */
            $container->get(JsonSender::class),
        ));
    }
}
