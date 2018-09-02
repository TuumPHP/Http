<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController
{
    use DispatchByMethodTrait;

    /**
     * @param ServerRequestInterface $request
     * @return null|ResponseInterface
     */
    public function __invoke($request)
    {
        return $this->dispatch($request);
    }
}