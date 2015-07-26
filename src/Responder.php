<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\AbstractWithViewData;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewStreamInterface;

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
     * @param View     $view
     * @param Redirect $redirect
     * @param Error    $error
     */
    public function __construct(
        View $view,
        Redirect $redirect,
        Error $error
    ) {
        $this->view     = $view;
        $this->redirect = $redirect;
        $this->error    = $error;
    }

    /**
     * @param ViewStreamInterface $view
     * @param ErrorViewInterface  $error
     * @param null|string         $content_view
     * @return static
     */
    public static function build(
        ViewStreamInterface $view,
        ErrorViewInterface $error,
        $content_view = null
    ) {
        $self = new static(
            new View($view, $content_view),
            new Redirect(),
            new Error($error)
        );

        return $self;
    }

    /**
     * @param SessionStorageInterface $session
     * @return static
     */
    public function withSession($session)
    {
        $self          = clone($this);
        $self->session = $session;

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
        return $responder->withSession($this->session)->withRequest($request, $response);
    }

    /**
     * @return SessionStorageInterface
     */
    public function getStorage()
    {
        return $this->session;
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
}