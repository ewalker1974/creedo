<?php

namespace Creedo\App\Http;

use Creedo\App\Attribute\Route;
use Creedo\App\Dto\HttpResponse;
use Creedo\App\Enum\HttpCode;
use Creedo\App\Enum\RequestMethod;
use Creedo\App\Enum\ValueType;
use Creedo\App\Exception\HttpException;
use ReflectionClass;

class Router
{
    /** @var array <int, ActionHandler> */
    private array $routes = [];

    public function addController(object $controller): void
    {
        $methods = (new ReflectionClass($controller))->getMethods();
        foreach ($methods as $method) {
            if (!$method->isPublic() || $method->isStatic()) {
                continue;
            }

            $attributes = $method->getAttributes(Route::class);
            if (empty($attributes)) {
                continue;
            }

            /** @var Route $attribute */
            $attribute = $attributes[0]->newInstance();

            $this->routes[] = new ActionHandler($attribute->method, $attribute->path, fn ($params) => $method->invokeArgs($controller, $params));
        }
    }

    /**
     * @throws HttpException
     * @throws \JsonException
     */
    public function handleRequest(): HttpResponse
    {
        $method = RequestMethod::from($_SERVER['REQUEST_METHOD']);
        $path = $_SERVER['REQUEST_URI'];

        $match = $this->matchRequest($method, $path);
        if ($match instanceof RequestContext) {
            $body = ValueType::UNDEFINED;
            if (in_array($method, [RequestMethod::POST, RequestMethod::PATCH, RequestMethod::PUT])) {
                $body = file_get_contents('php://input');
                if ($body === false) {
                    throw new HttpException('Invalid request body', HttpCode::HTTP_BAD_REQUEST->value);
                }
                $body = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            }

            $params = $body === ValueType::UNDEFINED ? $match->params : array_merge($match->params, ['body' => $body]);

            return $match->handler->handle($params);
        }

        throw new HttpException('Requested resource is not found', HttpCode::HTTP_NOT_FOUND->value);
    }

    private function matchRequest(RequestMethod $method, string $path): ?RequestContext
    {
        /** @var ActionHandler $handler */
        foreach ($this->routes as $handler) {
            if ($handler->method === $method) {
                $regex = $this->getRegexFromPath($handler->route);
                if (preg_match($regex, $path, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    return new RequestContext($handler, $params);
                }
            }
        }

        return null;
    }

    private function getRegexFromPath(string $path): string
    {
        return '#^'. str_replace('}', '>[a-z0-9]+)', str_replace('{', '(?P<', $path)). '$#';
    }
}
