<?php
namespace Tuum\Respond\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

trait ResponderHelperTrait
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Responder
     */
    private $responder;

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     */
    protected function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param null|string $string
     * @return ResponseInterface
     */
    protected function getResponse($string = null)
    {
        $response = $this->getResponder()->getResponse();
        if (is_string($string)) {
            $response->getBody()->write($string);
        }
        return $response;
    }

    /**
     * @param ResponseInterface $response
     */
    protected function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return Responder
     */
    protected function getResponder()
    {
        return $this->responder;
    }

    /**
     * @param Responder $responder
     */
    protected function setResponder($responder)
    {
        $this->responder = $responder;
    }

    /**
     * @return UploadedFileInterface[]
     */
    protected function getUploadFiles()
    {
        return $this->request->getUploadedFiles();
    }

    /**
     * @param null|string $name
     * @return array|null|object
     */
    protected function getPost($name = null)
    {
        if (is_null($name)) {
            return $this->request->getParsedBody();
        }
        $post = $this->request->getParsedBody();
        return array_key_exists($name, $post) ? $post[$name] : null;
    }

    /**
     * @return View
     */
    protected function view()
    {
        return $this->makeResponders('view');
    }

    /**
     * @return Redirect
     */
    protected function redirect()
    {
        return $this->makeResponders('redirect');
    }

    /**
     * @return Error
     */
    protected function error()
    {
        return $this->makeResponders('error');
    }

    /**
     * @return SessionStorageInterface
     */
    protected function session()
    {
        return $this->getResponder()->session();
    }

    /**
     * @return PayloadInterface
     */
    protected function getPayload()
    {
        return $this->getResponder()->getPayload($this->getRequest());
    }

    /**
     * @param mixed $presenter
     * @param array $data
     * @return ResponseInterface
     */
    protected function call($presenter, array $data = [])
    {
        return $this->view()
                    ->call($presenter, $data);
    }

    /**
     * @param string $type
     * @return mixed
     */
    private function makeResponders($type)
    {
        /** @var Responder\AbstractResponder $responder */
        $responder = $this->getResponder()->$type($this->getRequest(), $this->getResponse());
        return $responder;
    }
}