<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\SessionStorageInterface;

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
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     * @return View
     */
    public function view(
        ServerRequestInterface $request
    ) {
        return $this->builder->getView()->start($request, $this);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Redirect
     */
    public function redirect(
        ServerRequestInterface $request
    ) {
        return $this->builder->getRedirect()->start($request, $this);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Error
     */
    public function error(
        ServerRequestInterface $request
    ) {
        return $this->builder->getError()->start($request, $this);
    }

    /**
     * @return SessionStorageInterface
     */
    public function session()
    {
        return $this->builder->getSessionStorage();
    }

    public function getPayload(ServerRequestInterface $request): ?PayloadInterface
    {
        return Respond::getPayload($request);
    }
    
    public function setPayload(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withAttribute(PayloadInterface::class, $this->session()->getPayload());
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}