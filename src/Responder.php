<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

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
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return View
     */
    public function view(ServerRequestInterface $request, ResponseInterface $response = null)
    {
        return $this->view->withRequest($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Redirect
     */
    public function redirect(ServerRequestInterface $request, ResponseInterface $response = null)
    {
        return $this->redirect->withRequest($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface|null $response
     * @return Error
     */
    public function error(ServerRequestInterface $request, ResponseInterface $response = null)
    {
        return $this->error->withRequest($request, $response);
    }
}