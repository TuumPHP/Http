<?php
namespace Tuum\Respond\Service;

use Http\Factory\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Form\Data\Data;
use Tuum\Form\Data\Errors;
use Tuum\Form\Data\Escape;
use Tuum\Form\Data\Inputs;
use Tuum\Form\Data\Message;
use Tuum\Form\DataView;
use Tuum\Form\Dates;
use Tuum\Form\Forms;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

/**
 * Class ViewHelper
 *
 * @package Tuum\Respond\Service
 *
 * @property Forms   $forms
 * @property Dates   $dates
 * @property Data    $data
 * @property Message $message
 * @property Inputs  $inputs
 * @property Errors  $errors
 * @property Escape  $escape
 */
class ViewHelper
{
    /**
     * @var DataView
     */
    private $dataView;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * ViewHelper constructor.
     *
     * @param Responder|null   $responder
     */
    public function __construct(Responder $responder = null)
    {
        $this->responder = $responder;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Responder              $responder
     * @return ViewHelper
     */
    public static function forge(ServerRequestInterface $request, Responder $responder = null)
    {
        $self = new self($responder);
        $self->start($request, new DataView());

        return $self;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DataView|null          $dataView
     * @return $this
     */
    public function start(ServerRequestInterface $request, DataView $dataView = null)
    {
        $this->request  = $request;
        $this->payload  = $this->responder 
            ? $this->responder->getPayload($request)
            : Respond::getPayload($request);
        $this->dataView = $dataView;

        return $this;
    }
    
    /**
     * @return PayloadInterface
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }
        $method = 'get' . ucwords($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \InvalidArgumentException;
    }

    /**
     * @return Forms
     */
    public function forms()
    {
        if (!$this->dataView->inputs) {
            $this->dataView->setInputs($this->payload->getInput());
        }
        return $this->dataView->forms;
    }

    /**
     * @return Inputs
     */
    public function inputs()
    {
        if (!$this->dataView->inputs) {
            $this->dataView->setInputs($this->payload->getInput());
        }
        return $this->dataView->inputs;
    }

    /**
     * @return bool
     */
    public function hasInputs()
    {
        return $this->payload->hasInput();
    }

    /**
     * @return Data
     */
    public function data()
    {
        if (!$this->dataView->data) {
            $this->dataView->setData($this->payload->getData());
        }
        return $this->dataView->data;
    }

    /**
     * @return Errors
     */
    public function errors()
    {
        if (!$this->dataView->errors) {
            $this->dataView->setErrors($this->payload->getInputErrors());
        }
        return $this->dataView->errors;
    }

    /**
     * @return Message
     */
    public function message()
    {
        if (!$this->dataView->message) {
            $this->dataView->setMessage($this->payload->getMessages());
        }
        return $this->dataView->message;
    }

    /**
     * @return Dates
     */
    public function dates()
    {
        return $this->dataView->dates;
    }

    /**
     * return $request.
     * 
     * @return ServerRequestInterface
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * returns $uri object.
     * 
     * @return UriInterface
     */
    public function uri()
    {
        return $this->request->getUri();
    }

    /**
     * returns the request's attribute for $key.
     * 
     * @param null|string $key
     * @param null|string $default
     * @return array|mixed
     */
    public function attributes($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->request->getAttributes();
        }

        return $this->request->getAttribute($key, $default);
    }

    /**
     * call a presenter object and renders the content.
     * 
     * @param string|PresenterInterface $presenter
     * @param array                     $data
     * @return string
     */
    public function call($presenter, array $data = [])
    {
        $response = $this->responder
            ->view($this->request)
            ->call($presenter, $data);

        return $this->returnResponseBody($response);
    }

    /**
     * renders another template. 
     * 
     * @param string $viewFile
     * @param array  $data
     * @return string
     */
    public function render($viewFile, $data = [])
    {
        return $this->responder
            ->view($this->request)
            ->renderContents($viewFile, $data);
    }

    /**
     * returns an URI for named route, $routeName.
     * 
     * @param string $routeName
     * @param array  $options
     * @return string
     */
    public function route($routeName, $options = [])
    {
        return $this->responder->routes()->route($routeName, $options);
    }

    /**
     * returns an URI for $path. Maybe useful for site with $basePath.
     * 
     * @param string      $path
     * @param string|null $query
     * @return string
     */
    public function path($path, $query = null)
    {
        $uri = $this->request()->getUri();
        $uri = $uri->withPath($path);
        if ($query) {
            $uri = $uri->withQuery($query);
        }
        return (string) $uri;
    }
    
    /**
     * @param ResponseInterface $response
     * @return string
     */
    private function returnResponseBody($response)
    {
        if (!$response->getBody()->isSeekable()) {
            throw new \InvalidArgumentException('not seekable response body. ');
        }
        if ($this->responder->resolvable(StreamFactoryInterface::class)) {
            $stream = $response->getBody();
            $stream->rewind();
            return $stream->getContents();
        }
        $position = $response->getBody()->tell();
        $response->getBody()->rewind();
        $contents = $response->getBody()->read($position);
        $response->getBody()->rewind();

        return $contents;
    }

    /**
     * @return string
     */
    public function csrfToken()
    {
        return $this->responder->session($this->request)->getToken();
    }
}