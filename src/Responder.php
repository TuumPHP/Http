<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Service\ViewData;

class Responder
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Builder $builder
     * @return Responder
     */
    public static function forge(Builder $builder)
    {
        return new self($builder);
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
        $self = clone($this);
        $self->response = $response;
        return $self;
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
        $response = $response ?: $this->response;
        return $this->builder->getView()->start($request, $response);
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
        $response = $response ?: $this->response;
        return $this->builder->getRedirect()->start($request, $response);
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
        $response = $response ?: $this->response;
        return $this->builder->getError()->start($request, $response);
    }

    /**
     * @return SessionStorageInterface
     */
    public function session()
    {
        return $this->builder->getSessionStorage();
    }

    /**
     * @return ViewData
     */
    public function getViewData()
    {
        return $this->session()->getViewData();
    }
}