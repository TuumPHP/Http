<?php
namespace Tuum\Respond\Service\Renderer;

use League\Plates\Engine;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Service\ViewHelper;

class Plates implements RendererInterface
{
    /**
     * @var Engine
     */
    private $renderer;

    /**
     * @var string
     */
    public $viewName = 'view';

    /**
     * @param Engine   $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer   = $renderer;
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string   $root
     * @param callable $callable
     * @return Plates
     */
    public static function forge($root, $callable = null)
    {
        $renderer = new Engine($root);
        if (is_callable($callable)) {
            $renderer = call_user_func($callable, $renderer);
        }

        return new static($renderer);
    }

    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function render($template, ViewHelper $helper, array $data = [])
    {
        if (isset($helper)) {
            $this->renderer->addData([$this->viewName => $helper]);
        }
        return $this->renderer->render($template, $data);
    }
}