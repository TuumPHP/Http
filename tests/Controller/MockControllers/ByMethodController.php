<?php
namespace tests\Controller\MockControllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\AbstractController;
use Tuum\Respond\Controller\DispatchByMethodTrait;

class ByMethodController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    public function test($request)
    {
        return $this->dispatch($request);
    }
    
    public function onGet($test=null)
    {
        $response = $this->getResponse();
        $response->getBody()->write("test:{$test}");
        return $response;
    }

    public function onPost()
    {
        $response = $this->getResponse();
        $response->getBody()->write('test:post');
        return $response;
    }
}