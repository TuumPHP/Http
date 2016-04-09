<?php
namespace tests\Controller\MockControllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;

class ByMethodController
{
    use DispatchByMethodTrait;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return null|ResponseInterface
     */
    public function test($request, $response)
    {
        return $this->dispatch($request, $response);
    }
    
    public function onGet($test=null)
    {
        $this->getResponse()->getBody()->write("test:{$test}");
        return $this->response;
    }

    public function onPost()
    {
        $this->getResponse()->getBody()->write('test:post');
        return $this->response;
    }
}