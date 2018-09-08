<?php
namespace App\Demo\Handler;

use App\Demo\Routes;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tuum\Respond\Controller\Matcher;

class Dispatcher implements MiddlewareInterface
{
    /**
     * @var Routes
     */
    private $routes;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, Routes $routes)
    {
        $this->routes    = $routes;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $pathInfo = $request->getUri()->getPath();
        foreach ($this->routes as $name => [$route, $controller]) {
            $args = $this->match($pathInfo, $route);
            if (!empty($args)) {
                $request = $request->withQueryParams(array_merge($request->getQueryParams(), $args));
                return $this->run($controller, $request);
            }
        }

        return $handler->handle($request);
    }

    /**
     * @param string $pathInfo
     * @param string $route
     * @return array
     */
    private function match(string $pathInfo, string $route)
    {
        $matched = Matcher::verify($route, $pathInfo);
        return $matched;
    }

    /**
     * @param string|RequestHandlerInterface|callable $handler
     * @param ServerRequestInterface                  $request
     * @return ResponseInterface
     */
    private function run($handler, ServerRequestInterface $request): ResponseInterface
    {
        if (is_string($handler) && function_exists($handler)) {
            return $handler($request);
        }
        if (is_string($handler) && $this->container->has($handler)) {
            $handler = $this->container->get($handler);
        }
        if ($handler instanceof RequestHandlerInterface) {
            return $handler->handle($request);
        } 
        if (is_callable($handler)) {
            return $handler($request);
        }
        $message = "don't know how to handle a route: "
            . $request->getUri()->getPath();
        if (is_string($handler)) {
            $message .= ', handler: ' . $handler;
        }
        throw new \RuntimeException($message);
    }
}