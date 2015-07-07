<?php
namespace Tuum\Respond\Service;

use Tuum\Form\DataView;
use Tuum\Locator\Locator;
use Tuum\View\Renderer;

class ViewStream implements ViewStreamInterface
{
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
     * @param ViewData $data
     * @return array
     */
    private function setDataView($data)
    {
        if (!$data) {
            return [];
        }
        $view = new DataView();
        $view->setData($data->get(ViewData::DATA, []));
        $view->setErrors($data->get(ViewData::ERRORS, []));
        $view->setInputs($data->get(ViewData::INPUTS, []));
        $view->setMessage($data->get(ViewData::MESSAGE, []));
        return ['view' => $view];
    }

    /**
     * @param string   $view_file
     * @param ViewData $data
     * @return string
     */
    public function renderView($view_file, $data = null)
    {
        $self = clone($this);
        return $self->renderer->render($view_file, $self->setDataView($data));
    }
}