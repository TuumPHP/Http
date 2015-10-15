<?php
namespace Tuum\Respond\Service;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigStream implements ViewStreamInterface
{
    use ViewStreamTrait;

    /**
     * @var Twig_Environment
     */
    private $renderer;

    /**
     * @param Twig_Environment $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param string $root
     * @param array  $options
     * @return static
     */
    public static function forge($root, array $options = [])
    {
        $loader = new Twig_Loader_Filesystem($root);
        $twig   = new Twig_Environment($loader, $options);
        return new static($twig);
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
        $view_file       = substr($view_file, -4) === '.twig' ?: $view_file . '.twig';
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
        $this->renderer->addGlobal('viewData', $view);
        $this->view_data = $data->getRawData();
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