<?php
namespace App\App;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;

class Dispatcher
{
    private $routes = [];

    private $container = [];

    public function __construct(array $config = [])
    {
        $this->container = $config;
    }

    /**
     * @param $key
     * @return null|mixed
     */
    public function get($key)
    {
        return array_key_exists($key, $this->container) ? $this->container[$key] : null;
    }

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
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function run($request, $response)
    {
        $pathInfo = ReqAttr::getPathInfo($request);
        foreach ($this->routes as $path => $app) {
            if ($path === $pathInfo) {
                $app = $this->resolve($app);
                return $app($request, $response);
            }
        }
        return null;
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
            return call_user_func([$app, 'forge'], $this);
        }
        throw new \InvalidArgumentException;
    }

    /**
     * @return Closure
     */
    public function getResolver()
    {
        return function($key) {
            return $this->resolve($key);
        };
    }

    /**
     * @param string $key
     * @param mixed  $service
     */
    public function set($key, $service)
    {
        $this->container[$key] = $service;
    }
}