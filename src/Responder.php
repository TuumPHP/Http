<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\AbstractWithViewData;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Presenter;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Responder\ViewData;

class Responder
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var Error
     */
    private $error;

    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var ViewData
     */
    private $viewData;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Presenter
     */
    private $presenter;

    /**
     * @param View      $view
     * @param Redirect  $redirect
     * @param Error     $error
     * @param ViewData  $viewData
     * @param Presenter $presenter
     */
    public function __construct(
        View $view,
        Redirect $redirect,
        Error $error,
        $viewData = null,
        Presenter $presenter = null
    ) {
        $this->view     = $view;
        $this->redirect = $redirect;
        $this->error    = $error;
        $this->viewData = $viewData ?: new ViewData();
        $this->presenter = $presenter ?: new Presenter();
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
        $data          = $session->getFlash(ViewData::MY_KEY);
        if ($data) {
            // if ViewData is taken from the session,
            // detach it from the object in the session.
            $self->viewData = clone($data);
        }

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
     * modifies viewData.
     *
     * @api
     * @param callable $closure
     * @return Responder
     */
    public function withViewData(callable $closure)
    {
        $self           = clone($this);
        $self->viewData = $closure($this->viewData);

        return $self;
    }

    /**
     * @param AbstractWithViewData   $responder
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return AbstractWithViewData
     */
    private function returnWith($responder, $request, $response)
    {
        $response = $response ?: $this->response;

        return $responder->withRequest($request, $response, $this->session, $this->viewData);
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
        return $this->returnWith($this->view, $request, $response);
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
        return $this->returnWith($this->redirect, $request, $response);
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
        return $this->returnWith($this->error, $request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Presenter
     */
    public function presenter(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        return $this->returnWith($this->presenter, $request, $response);
    }

    /**
     * @return SessionStorageInterface
     */
    public function session() {
        return $this->session;
    }
}