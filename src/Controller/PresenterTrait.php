<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Respond;

trait PresenterTrait
{
    use ResponderHelperTrait;

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, ViewDataInterface $viewData)
    {
        return $this->_dispatch('dispatch', $request, $response, $viewData);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return ResponseInterface
     */
    public function __call($name, $arguments)
    {
        return $this->_dispatch($name, $arguments[0], $arguments[1], $arguments[2]);
    }

    /**
     * @param string                 $name
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array|mixed            $data
     * @return ResponseInterface
     */
    public function _dispatch($name, ServerRequestInterface $request, ResponseInterface $response, $data)
    {
        $this->request = $request;
        $this->response = $response;
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }

        return $this->$name($data);
    }
}
