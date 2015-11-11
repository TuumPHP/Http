<?php
namespace Tuum\Respond\Helper;

use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\TwigViewer;
use Tuum\Respond\Service\ViewerInterface;

/**
 * Class Builder
 *
 * experimental builder for constructing Responder and its associated objects.
 * it works, but not sure if this is useful, or maintainable.
 *
 * @package Tuum\Respond\Helper
 */
class Builder
{
    /**
     * @var string
     */
    private $contents_file;

    /**
     * Builder constructor.
     *
     * @param string $contents_file
     */
    private function __construct($contents_file = null)
    {
        $this->contents_file = $contents_file;
    }

    /**
     * @param string $contents_file
     * @return Builder
     */
    public static function forge($contents_file = null)
    {
        return new self($contents_file);
    }

    /**
     * @param string $root
     * @param array  $options
     * @param null   $callable
     * @return ErrorBuilder
     */
    public function buildTwig($root, $options = [], $callable = null)
    {
        $viewer = TwigViewer::forge($root, $options, $callable);
        return new ErrorBuilder(
            new View($viewer, $this->contents_file),
            $viewer
        );
    }

    /**
     * @param string $root
     * @param null   $callable
     * @return ErrorBuilder
     */
    public function buildTuum($root, $callable = null)
    {
        $viewer = TuumViewer::forge($root, $callable);
        return new ErrorBuilder(
            new View($viewer, $this->contents_file),
            $viewer
        );
    }
}

class ErrorBuilder
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var ViewerInterface
     */
    private $errorViewer;

    /**
     * @var array
     */
    private $methods = [];

    /**
     * ErrorBuilder constructor.
     *
     * @param View            $view
     * @param ViewerInterface $viewer
     */
    public function __construct(View $view, ViewerInterface $viewer)
    {
        $this->view        = $view;
        $this->errorViewer = $viewer;
    }

    /**
     * @param ViewerInterface $view
     * @return $this
     */
    public function useAnotherViewerForError(ViewerInterface $view)
    {
        $this->errorViewer = $view;
        return $this;
    }

    /**
     * @param string $method
     * @param int    $status
     * @return $this
     */
    public function addMethods($method, $status)
    {
        $this->methods[$method] = $status;
        return $this;
    }

    /**
     * @param array $options
     * @return ResponderBuilder
     */
    public function buildErrorView($options = [])
    {
        $errorView   = ErrorView::forge($this->errorViewer, $options);
        $error = new Error($errorView);
        return new ResponderBuilder($this->view, $error);
    }
}

class ResponderBuilder
{
    /**
     * @var null|ViewData|mixed
     */
    private $viewData;

    /**
     * @var View
     */
    private $view;

    /**
     * @var Error
     */
    private $error;

    /**
     * ResponderBuilder constructor.
     *
     * @param View  $view
     * @param Error $error
     */
    public function __construct(View $view, Error $error)
    {
        $this->view = $view;
        $this->error = $error;
    }

    /**
     * @param mixed $view
     * @return $this
     */
    public function useAsViewData($view)
    {
        $this->viewData = $view;
        return $this;
    }

    /**
     * @return Responder
     */
    public function build()
    {
        return new Responder(
            $this->view,
            new Responder\Redirect(),
            $this->error,
            $this->viewData
        );
    }

    /**
     * @param string $name
     * @return Responder
     */
    public function buildWithSession($name = 'tuum-app')
    {
        return $this->build()->withSession(SessionStorage::forge($name));
    }
}