<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\PresenterInterface;

abstract class AbstractPresenter implements PresenterInterface
{
    use PresentByContentTrait;

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param array                  $data
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    public function __invoke(ServerRequestInterface $request, array $data = [])
    {
        return $this->_dispatch($request, $data);
    }

}