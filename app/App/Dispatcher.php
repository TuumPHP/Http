<?php
namespace App\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\Matcher;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Responder;

class Dispatcher
{
    private $routes = [];

    /**
     * @var ContainerInterface;
     */
    private $container = [];

    /**
     * Dispatcher constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    /**
     * @param $key
     * @return null|mixed
     */
    public function get($key)
    {
        return $this->container->get($key);
    }

    /**
     * @param string  $path
     * @param callable $handler
     */
    public function add($path, $handler)
    {
        $this->routes[$path] = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $request = $this->get(Responder::class)->setPayload($request);
        try {

            if ($response_returned = $this->_run($request)) {
                return $response_returned;
            }
            $responder = $this->get(Responder::class);
            return $responder->error($request)->notFound();
            
        } catch (\Exception $e) {
            /** @var Responder\Error $error */
            $error = $this->get(Responder::class)->error($request);
            $status = $e->getCode() ?: 500;
            if ($this->container->has('debug') && $this->container->get('debug')) {
                return $error->asView($status, ['exception' => $e]);
            }
            return $error->asView($status);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function _run(ServerRequestInterface $request): ?ResponseInterface
    {
        $pathInfo = ReqAttr::getPathInfo($request);
        foreach ($this->routes as $path => $app) {
            if ($args = $this->match($path, $pathInfo)) {
                if (!is_callable($app)) {
                    $app = $this->container->get($app);
                }
                return $app($request, $args);
            }
        }
        return null;
    }

    /**
     * @param string $path
     * @param string $pathInfo
     * @return bool|array
     */
    private function match($path, $pathInfo)
    {
        $matched = Matcher::verify($path, $pathInfo);
        return empty($matched) ? false : $matched;
    }
}