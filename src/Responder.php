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
        if ($this->session) {
            $viewData = $this->session->getFlash(ViewDataInterface::MY_KEY);
            if ($viewData) {
                return clone($viewData);
            }
        }

        $forger = $this->getViewDataForger();
        return $forger();
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
    public function withViewDataForger($callable)
    {
        $self                 = clone($this);
        $self->viewDataForger = $callable;
        return $self;
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
     * @param string                 $responder
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return AbstractWithViewData
     */
    private function returnWith($responder, $request, $response)
    {
        $responder = $this->responders[$responder];
        $response  = $response ?: $this->response;

        return $responder->withRequest($request, $response, $this->session);
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