<?php
namespace Tuum\Respond\Service;

use RuntimeException;
use Tuum\Locator\Locator;
use Tuum\View\Renderer;

class ViewStream implements ViewStreamInterface
{
    use ViewStreamTrait;

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
        if (!$data) {
            return;
        }
        $view = $this->forgeDataView($data);
        $this->view_data = $data->getRawData();
        $this->view_data = ['view' => $view];
    }

    /**
     * @return string
     */
    protected function render()
    {
        if (!$this->view_file) {
            throw new \RuntimeException('no view file to render');
        }
        return $this->renderer->render($this->view_file, $this->view_data);
    }

    /**
     * modifies the internal renderer's setting.
     *
     * $modifier = function($renderer) {
     *    // modify the renderer.
     * }
     *
     * @param \Closure $modifier
     * @return mixed
     */
    public function modRenderer($modifier)
    {
        $modifier = $modifier->bindTo($this, $this);
        return $modifier($this->renderer);
    }

}