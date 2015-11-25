<?php
namespace App\App;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;

class Dispatcher
{
    private $routes = [];

    /**
     * @param string  $path
     * @param Closure $handler
     */
    public function add($path, $handler)
    {
        $this->routes[$path] = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run($request)
    {
        $response = null;
        $pathInfo = ReqAttr::getPathInfo($request);
        foreach ($this->routes as $path => $app) {
            if ($path === $pathInfo) {
                $app = $this->resolve($app);
                $response = $app($request);
                break;
            }
        }
        return $response;
    }

    /**
     * @param mixed $app
     * @return callable
     */
    private function resolve($app)
    {
        if (is_callable($app)) {
            return $app;
        }
        if (is_string($app) && class_exists($app)) {
            return call_user_func([$app,'forge']);
        }
        throw new \InvalidArgumentException;
    }
}