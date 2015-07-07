<?php
namespace Tuum\Respond\Service;

use Closure;
use Tuum\Form\DataView;
use Tuum\Locator\Locator;
use Tuum\View\Renderer;

class ViewStream implements ViewStreamInterface
{
    /**
     * @var string|array|Closure
     */
    private $view_file;

    /**
     * @var array
     */
    private $view_data = [];

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @param Renderer $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string $root
     * @return static
     */
    public static function forge($root)
    {
        $renderer = new Renderer(new Locator($root));
        return new static($renderer);
    }

    /**
     * sets view template file and data to be rendered.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return ViewStreamInterface
     */
    public function withView($view_file, $data = null)
    {
        $self            = clone($this);
        $self->view_file = $view_file;
        $self->setDataView($data);
        return $self;
    }

    /**
     * @param ViewData $data
     */
    private function setDataView($data)
    {
        if(!$data) return;
        $view = new DataView();
        $view->setData($data->get(ViewData::DATA, []));
        $view->setErrors($data->get(ViewData::ERRORS, []));
        $view->setInputs($data->get(ViewData::INPUTS, []));
        $view->setMessage($data->get(ViewData::MESSAGE, []));
        $this->view_data['view'] = $view;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->renderer->render($this->view_file, $this->view_data);
    }

    /**
     * @param string   $view_file
     * @param ViewData $data
     * @return string
     */
    public function renderView($view_file, $data = null)
    {
        $self            = clone($this);
        return $self->withView($view_file, $data)->render();
    }
}