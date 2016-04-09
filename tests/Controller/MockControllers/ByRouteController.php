<?php
namespace tests\Controller\MockControllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Controller\DispatchByRouteTrait;

class ByRouteController
{
    use DispatchByRouteTrait;

    protected function getRoutes()
    {
        return [
            'get:/'          => 'get',
            'get:/my/{name}' => 'name',
        ];
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return null|ResponseInterface
     */
    public function test($request, $response)
    {
        return $this->dispatch($request, $response);
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