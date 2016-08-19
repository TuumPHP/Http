<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

trait ResponderTrait
{
    /**
     * @return Responder
     */
    abstract protected function getResponder();

    /**
     * @return ServerRequestInterface
     */
    abstract protected function getRequest();

    /**
     * @param null|string $string
     * @return ResponseInterface
     */
    abstract protected function getResponse($string = null);

    /**
     * @param null|ViewDataInterface $viewData
     * @return View
     */
    protected function view($viewData = null)
    {
        return $this->makeResponders('view', $viewData);
    }

    /**
     * @param null|ViewDataInterface $viewData
     * @return Redirect
     */
    protected function redirect($viewData = null)
    {
        return $this->makeResponders('redirect', $viewData);
    }

    /**
     * @param null|ViewDataInterface $viewData
     * @return Error
     */
    protected function error($viewData = null)
    {
        return $this->makeResponders('error', $viewData);
    }

    /**
     * @return ViewDataInterface
     */
    protected function getViewData()
    {
        return $this->getResponder()->getViewData();
    }

    /**
     * @param string $type
     * @param null|ViewDataInterface $viewData
     * @return mixed
     */
    private function makeResponders($type, $viewData = null)
    {
        /** @var Responder\AbstractWithViewData $responder */
        $responder = $this->getResponder()->$type($this->getRequest(), $this->getResponse());
        if ($viewData instanceof ViewDataInterface) {
            $responder = $responder->withView($viewData);
        }
        return $responder;
    }
}