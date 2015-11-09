<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewData;

/**
 * Class AbstractWithViewData
 *
 * @package Tuum\Respond\Responder
 *          
 * @method static|$this withSuccess($message)
 * @method static|$this withAlert($message)
 * @method static|$this withError($message)
 * @method static|$this withInputData(array $array)
 * @method static|$this withInputErrors(array $array)
 * @method static|$this withData($key, $value)
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
     * @var SessionStorageInterface
     */
    protected $session;

    /**
     * @var ViewData
     */
    protected $data;

    /**
     * initializes responders with $request, $response, $viewData,
     * and optionally $session. intended to be internal API used
     * by Responder object and tests. 
     * 
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param SessionStorageInterface $session
     * @param ViewData                $viewData
     * @return $this
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response,
        SessionStorageInterface $session,
        ViewData $viewData
    ) {
        $self           = clone($this);
        $self->request  = $request;
        $self->response = $response;
        $self->data     = $viewData;
        $self->session  = $session;

        return $self;
    }

    /**
     * @param string $method
     * @param array  $args
     * @return $this|static
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 4) === 'with') {
            return $this->callViewData('set'.substr($method, 4), $args);
        }
        throw new \BadMethodCallException;
    }

    /**
     * @param string $method
     * @param array  $args
     * @return static|$this
     */
    private function callViewData($method, $args)
    {
        $self = clone($this);
        $self->data = clone($this->data);
        call_user_func_array([$self->data, $method], $args); // let it fail!

        return $self;
    }

    /**
     * copies request's attributes into data using keys.
     *
     * @api
     * @param string $arg
     * @return $this
     */
    public function withReqAttribute($arg)
    {
        $args       = func_get_args();
        $self       = clone($this);
        $self->data = clone($this->data);
        foreach ($args as $key) {
            $self->data->setData($key, $this->request->getAttribute($key));
        }

        return $self;
    }

    /**
     * @api
     * @param string       $key
     * @param mixed        $value
     * @return $this
     */
    public function withFlashData($key, $value)
    {
        if (!isset($this->session)) {
            throw new \BadMethodCallException('SessionStorageInterface not defined.');
        }
        $this->session->setFlash($key, $value);

        return $this;
    }

    /**
     * modifies viewData.
     *
     * @api
     * @param callable $closure
     * @return static
     */
    public function withViewData(callable $closure)
    {
        $self       = clone($this);
        $data       = clone($this->data);
        $self->data = $closure($data);

        return $self;
    }
}