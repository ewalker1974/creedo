<?php

namespace Creedo\App\Http;

use Creedo\App\ErrorHandler\HttpErrorHandler;
use Throwable;

readonly class RequestHandler
{
    public function __construct(
        private Router $router,
        private HttpErrorHandler $errorHandler,
        private JsonSender $sender,
    ) {
    }

    public function handle(): void
    {
        try {
            $response = $this->router->handleRequest();
        } catch (Throwable $e) {
            $response = $this->errorHandler->handleException($e);
        }

        $this->sender->send($response);
    }
}
