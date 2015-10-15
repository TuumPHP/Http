<?php
namespace Tuum\Respond\Service;

use RuntimeException;
use Tuum\Form\DataView;
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
        $view = new DataView();
        $view->setData($data->getData());
        $view->setErrors($data->getInputErrors());
        $view->setInputs($data->getInputData());
        $view->setMessage($data->getMessages());

        $this->view_data = $data->getRawData();
        $this->view_data = ['view' => $view];
    }

    /**
     * @return string
     */
    private function render()
    {
        if (!$this->view_file) {
            throw new \RuntimeException('no view file to render');
        }
        return $this->renderer->render($this->view_file, $this->view_data);
    }

    /**
     * @return resource
     */
    protected function getResource()
    {
        if (is_null($this->fp)) {
            $this->fp = fopen('php://temp', 'wb+');
            fwrite($this->fp, $this->render());
        }

        return $this->fp;
    }

    /**
     * @return Renderer
     */
    protected function getRenderer()
    {
        return $this->renderer;
    }
}