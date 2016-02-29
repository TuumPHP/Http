<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * initializes responders with $request, $response, $viewData,
     * and optionally $session. intended to be internal API used
     * by Responder object and tests.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @return $this
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $self           = clone($this);
        $self->request  = $request;
        $self->response = $response;

        return $self;
    }
}