<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;

trait PresenterTrait
{
    use ResponderHelperTrait;

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param array                  $data
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, array $data = [])
    {
        return $this->_dispatch('dispatch', $request, $data);
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
     * @param array                  $data
     * @return ResponseInterface
     */
    public function _dispatch($name, ServerRequestInterface $request, array $data)
    {
        $this->request = $request;
        if (!$this->responder) {
            $this->responder = Respond::getResponder();
        }

        return $this->$name($data);
    }
}
