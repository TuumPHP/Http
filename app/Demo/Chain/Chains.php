<?php
namespace App\Demo\Chain;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Chains implements RequestHandlerInterface
{
    /**
     * @var array
     */
    private $chains;
    
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(array $chains, ContainerInterface $container)
    {
        $this->chains = $chains;
        $this->container = $container;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $chain      = current($this->chains);
        $middleware = is_string($chain) 
            ? $this->container->get($chain) 
            : $chain;
        next($this->chains);
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
        if (is_callable($middleware)) {
            return $middleware($request, $this);
        }
        throw new \RuntimeException('cannot handle a middleware');
    }
}