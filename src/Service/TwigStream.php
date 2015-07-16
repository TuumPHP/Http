<?php
namespace Tuum\Respond\Service;

use Tuum\Form\DataView;
use Twig_Environment;
use Twig_SimpleFunction;

class TwigStream implements ViewStreamInterface
{
    use ViewStreamTrait;

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
        $loader = new \Twig_Loader_Filesystem($root);
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
        $view_file       = substr($view_file, -4) === '.twig' ?: $view_file.'.twig';
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
        $view->setData($data->get(ViewData::DATA, []));
        $view->setErrors($data->get(ViewData::ERRORS, []));
        $view->setInputs($data->get(ViewData::INPUTS, []));
        $view->setMessage($data->get(ViewData::MESSAGE, []));

        $this->renderer->addGlobal('viewData', $view);
        $this->view_data = $data->get(ViewData::DATA, []);
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
     * @return Twig_Environment
     */
    protected function getRenderer()
    {
        return $this->renderer;
    }

}