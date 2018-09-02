<?php
namespace tests\Controller\MockControllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\DispatchByRouteTrait;

class ByRouteController
{
    use DispatchByRouteTrait;

    protected function getRoutes()
    {
        return [
            'get:/'          => 'onGet',
            'get:/my/{name}' => 'onName',
        ];
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    public function test($request)
    {
        return $this->dispatch($request);
    }

    public function onGet()
    {
        return $this->getResponse('route:get');
    }
    
    public function onName($name)
    {
        return $this->getResponse("route:{$name}");
    }
}