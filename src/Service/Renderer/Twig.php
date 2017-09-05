<?php
namespace Tuum\Respond\Service\Renderer;

use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Service\ViewHelper;
use Twig_Environment;
use Twig_Loader_Filesystem;

class Twig   implements RendererInterface
{
    /**
     * @var Twig_Environment
     */
    private $renderer;

    /**
     * @var string
     */
    public $viewName = 'view';

    /**
     * @param Twig_Environment $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer   = $renderer;
    }

    /**
     * @param string   $root
     * @param array    $options
     * @param callable $callable
     * @return static
     */
    public static function forge($root, array $options = [], $callable = null)
    {
        $loader = new Twig_Loader_Filesystem($root);
        $twig   = new Twig_Environment($loader, $options);
        if (is_callable($callable)) {
            $twig = call_user_func($callable, $twig);
        }

        return new static($twig);
    }

    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function render($template, ViewHelper $helper, array $data = [])
    {
        $viewFile   = (substr($template, -5) === '.twig') ?: $template . '.twig';
        $this->renderer->addGlobal($this->viewName, $helper);

        return $this->renderer->render($viewFile, $data);
    }
}