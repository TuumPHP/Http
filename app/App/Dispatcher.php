<?php
namespace App\App;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Responder;

class Dispatcher
{
    private $routes = [];

    /**
     * @var Container;
     */
    private $container = [];

    public function __construct(array $config = [])
    {
        $this->container = new Container($config);
    }

    /**
     * @return Container
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
        try {

            $response = $this->_run($request, $response);
            if (!$response) {
                $responder = $this->get(Responder::class);
                $response = $responder->error($request, $response)->notFound();
            }
            return $response;
            
        } catch (\Exception $e) {
            $responder = $this->get(Responder::class);
            return $responder->error($request, $response)->asView(500);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function _run($request, $response)
    {
        $pathInfo = ReqAttr::getPathInfo($request);
        foreach ($this->routes as $path => $app) {
            if ($args = $this->match($path, $pathInfo)) {
                if (!is_callable($app)) {
                    $app = $this->container->get($app);
                }
                return $app($request, $response, $args);
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
        if (preg_match("!^{$path}$!", $pathInfo, $matched)) {
            return $matched;
        }
        return false;
    }
}