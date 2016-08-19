<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder\AbstractWithViewData;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Responder\ViewData;

class Responder
{
    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var AbstractWithViewData[]
     */
    private $responders = [];

    /**
     * @var callable
     */
    private $viewDataForger;

    /**
     * @var ViewDataInterface
     */
    private $viewData;

    /**
     * @param View     $view
     * @param Redirect $redirect
     * @param Error    $error
     */
    public function __construct($view, $redirect, $error)
    {
        $this->responders = [
            'view'     => $view,
            'redirect' => $redirect,
            'error'    => $error,
        ];
    }

    /**
     * @return mixed|ViewDataInterface
     */
    public function getViewData()
    {
        if (isset($this->viewData)) {
            return $this->viewData;
        }
        if ($this->session) {
            if ($viewData = $this->session->getFlash(ViewDataInterface::MY_KEY)) {
                $this->viewData = clone($viewData);
                return $this->viewData;
            }
        }

        $forger = $this->getViewDataForger();
        $this->viewData = $forger();
        return $this->viewData;
    }

    /**
     * @return callable
     */
    protected function getViewDataForger()
    {
        if ($this->viewDataForger) {
            return $this->viewDataForger;
        }
        return function () {
            return new ViewData();
        };
    }

    /**
     * @param callable $callable
     * @return Responder
     */
    public function setViewDataForger($callable)
    {
        $this->viewDataForger = $callable;
        return $this;
    }

    /**
     * set SessionStorage and retrieves ViewData from session's flash.
     * execute this method before using responders.
     *
     * @api
     * @param SessionStorageInterface $session
     * @return Responder
     */
    public function withSession(SessionStorageInterface $session)
    {
        $self          = clone($this);
        $self->session = $session;

        return $self;
    }

    /**
     * set response object when omitting $response when calling
     * responders, such as:
     * Respond::view($request);
     *
     * responders will return $response using this object.
     *
     * @api
     * @param ResponseInterface $response
     * @return Responder
     */
    public function withResponse(ResponseInterface $response)
    {
        $self           = clone($this);
        $self->response = $response;

        return $self;
    }

    /**
     * @param ViewDataInterface|callable $data
     * @return $this
     */
    public function withView($data)
    {
        $self           = clone($this);
        $self->viewData = is_callable($data) ? $data(clone($this->getViewData())) : $data;
        return $self;
    }

    /**
     * @param string                 $responder
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return AbstractWithViewData
     */
    private function returnWith($responder, $request, $response)
    {
        $responder = $this->responders[$responder];
        $response  = $response ?: $this->response;

        return $responder->withRequest($request, $response, $this);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return View
     */
    public function view(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('view', $request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Redirect
     */
    public function redirect(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('redirect', $request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Error
     */
    public function error(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith('error', $request, $response);
    }

    /**
     * @return SessionStorageInterface
     */
    public function session()
    {
        return $this->session;
    }
}