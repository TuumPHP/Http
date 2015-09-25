<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\ControllerTrait;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Responder;
use Tuum\Respond\ResponseHelper;

class PageController
{
    use ControllerTrait;

    use DispatchByMethodTrait;

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return PageController
     */
    public static function forge()
    {
        return new self();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function invoke(ServerRequestInterface $request)
    {
        $response = ResponseHelper::createResponse('');
        return $this->invokeController($request, $response);
    }

    /**
     * @param null|string $name
     * @return ResponseInterface
     */
    public function onGet($name = null)
    {
        return $this->view()
            ->with('name', $name ?: '** not set **')
            ->asView('top');
    }

    /**
     * @param null|string $view
     * @return ResponseInterface
     */
    public function onView($view = null)
    {
        return $this->view()
            ->with('view', $view)
            ->asView('view');
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        $name = $this->getPost('name');
        return $this->view()
            ->with('name', $name)
            ->asView('post');
    }
}