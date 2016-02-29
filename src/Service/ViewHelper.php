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
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Responder\ViewData;

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
    private $view;

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
     * @return ViewHelper
     */
    public static function forge()
    {
        return new self(new DataView());
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param View                   $view
     * @return $this
     */
    public function start($request, $response, $view = null)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->view     = $view ?: $request->getAttribute(View::class, null);

        return $this;
    }

    /**
     * @param ViewDataInterface $viewData
     * @return $this
     */
    public function setViewData($viewData)
    {
        $view = $this->dataView;
        $get  = function ($method) use ($viewData) {
            return ($viewData instanceof ViewDataInterface) ? $viewData->$method() : [];
        };
        $view->setData($get('getData'));
        $view->setErrors($get('getInputErrors'));
        $view->setInputs($get('getInputData'));
        $view->setMessage($get('getMessages'));
        $this->viewData = $viewData;

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->dataView->$name)) {
            return $this->dataView->$name;
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
        return $this->dataView->inputs;
    }

    /**
     * @return Data
     */
    public function data()
    {
        return $this->dataView->data;
    }

    /**
     * @return Errors
     */
    public function errors()
    {
        return $this->dataView->errors;
    }

    /**
     * @return Message
     */
    public function message()
    {
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
     * @param null|mixed|ViewData       $viewData
     * @return string
     */
    public function call($presenter, $viewData = null)
    {
        if (!$this->view) {
            return '';
        }
        $this->response->getBody()->rewind();
        $viewData = $viewData ?: $this->viewData;
        $response = $this->view->call($presenter, $viewData);

        return $this->returnResponseBody($response);
    }

    /**
     * @param string              $viewFile
     * @param null|mixed|ViewData $viewData
     * @return string
     */
    public function render($viewFile, $viewData = null)
    {
        if (!$this->view) {
            return '';
        }
        $this->response->getBody()->rewind();
        $viewData = $viewData ?: $this->viewData;
        $response = $this->view->render($viewFile, $viewData);

        return $this->returnResponseBody($response);
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
        $response->getBody()->rewind();

        $contents = $response->getBody()->getContents();
        $response->getBody()->rewind();

        return $contents;
    }
}