<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder\View;

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
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var View
     */
    private $renderer;

    /**
     * @var ViewData
     */
    private $viewData;

    /**
     * ViewHelper constructor.
     *
     * @param DataView $dataView
     */
    public function __construct($dataView)
    {
        $this->dataView = $dataView;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewDataInterface      $viewData
     * @param View     $responder
     * @return ViewHelper
     */
    public static function forge($request, $response, $viewData, $responder)
    {
        $self = new self(new DataView());
        $self->start($request, $response, $responder);
        $self->setViewData($viewData);

        return $self;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param View     $renderer
     * @return $this
     */
    public function start($request, $response, $renderer)
    {
        $this->request   = $request;
        $this->response  = $response;
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @param ViewDataInterface $viewData
     * @return $this
     */
    public function setViewData($viewData)
    {
        $this->viewData = $viewData;

        return $this;
    }

    /**
     * @return ViewData
     */
    public function getViewData()
    {
        return $this->viewData;
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
        return $this->dataView->forms;
    }

    /**
     * @return Inputs
     */
    public function inputs()
    {
        if (!$this->dataView->inputs) {
            $this->dataView->setInputs($this->viewData->getInput());
        }
        return $this->dataView->inputs;
    }

    /**
     * @return Data
     */
    public function data()
    {
        if (!$this->dataView->data) {
            $this->dataView->setData($this->viewData->getData());
        }
        return $this->dataView->data;
    }

    /**
     * @return Errors
     */
    public function errors()
    {
        if (!$this->dataView->errors) {
            $this->dataView->setErrors($this->viewData->getInputErrors());
        }
        return $this->dataView->errors;
    }

    /**
     * @return Message
     */
    public function message()
    {
        if (!$this->dataView->message) {
            $this->dataView->setMessage($this->viewData->getMessages());
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
     * @return ServerRequestInterface
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return UriInterface
     */
    public function uri()
    {
        return $this->request->getUri();
    }

    /**
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
     * @param string|PresenterInterface $presenter
     * @param null|mixed|ViewData       $data
     * @return string
     */
    public function call($presenter, array $data = [])
    {
        if (!$this->renderer) {
            return '';
        }
        $response = $this->renderer->call($presenter, $data);

        return $this->returnResponseBody($response);
    }

    /**
     * @param string              $viewFile
     * @param array $data
     * @return string
     */
    public function render($viewFile, $data = [])
    {
        if (!$this->renderer) {
            return '';
        }
        return $this->renderer
            ->start($this->request, $this->response)
            ->renderContents($viewFile, $data);
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
        $position = $response->getBody()->tell();
        $response->getBody()->rewind();
        $contents = $response->getBody()->read($position);
        $response->getBody()->rewind();

        return $contents;
    }
}