<?php
namespace App\Demo\Chain;

use App\Demo\Middleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    /**
     * @var Middleware
     */
    private $middleware;
    
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, Middleware $middleware)
    {
        $this->container = $container;
        $this->middleware = $middleware;
    }
    
    public function run(ServerRequestInterface $request) : ResponseInterface
    {
        $chains = $this->middleware->get();
        reset($chains);
        $handler = new Chains($chains, $this->container);
        return $handler->handle($request);
    }
}