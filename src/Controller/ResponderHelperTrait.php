<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

trait ResponderHelperTrait
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
     * @return View
     */
    protected function view()
    {
        return $this->makeResponders('view');
    }

    /**
     * @return Redirect
     */
    protected function redirect()
    {
        return $this->makeResponders('redirect');
    }

    /**
     * @return Error
     */
    protected function error()
    {
        return $this->makeResponders('error');
    }

    /**
     * @return SessionStorageInterface
     */
    protected function session()
    {
        return $this->getResponder()->session();
    }

    /**
     * @return ViewDataInterface
     */
    protected function getViewData()
    {
        return $this->getResponder()->getViewData();
    }

    /**
     * @param mixed $presenter
     * @param array $data
     * @return ResponseInterface
     */
    protected function call($presenter, array $data = [])
    {
        return $this->getResponder()
                    ->view($this->getRequest(), $this->getResponse())
                    ->call($presenter, $data);
    }

    /**
     * @param string $type
     * @return mixed
     */
    private function makeResponders($type)
    {
        /** @var Responder\AbstractResponder $responder */
        $responder = $this->getResponder()->$type($this->getRequest(), $this->getResponse());
        return $responder;
    }
}