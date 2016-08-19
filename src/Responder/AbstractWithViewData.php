<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;

/**
 * Class AbstractWithViewData
 *
 * @package Tuum\Respond
 *
 * @method static |$this withSuccess($message)
 * @method static |$this withAlert($message)
 * @method static |$this withError($message)
 * @method static |$this withInputData(array $array)
 * @method static |$this withInputErrors(array $array)
 * @method static |$this withData($key, $value)
 */
abstract class AbstractWithViewData
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ViewDataInterface
     */
    protected $viewData;

    /**
     * @var Responder
     */
    protected $responder;

    /**
     * initializes responders with $request, $response, $viewData,
     * and optionally $session. intended to be internal API used
     * by Responder object and tests.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Responder              $responder
     * @return $this
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Responder $responder
    ) {
        $self           = clone($this);
        $self->request  = $request;
        $self->response = $response;
        $self->responder = $responder;
        $self->viewData  = clone($responder->getViewData());

        return $self;
    }
    
    /**
     * @param ViewDataInterface|callable $data
     * @return $this
     */
    public function withView($data)
    {
        $self           = clone($this);
        $self->viewData = is_callable($data) ? $data(clone($this->viewData)) : $data;
        return $self;
    }
}