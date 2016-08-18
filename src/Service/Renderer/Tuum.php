<?php
namespace Tuum\Respond\Service\Renderer;

use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Service\ViewHelper;
use Tuum\View\Renderer;

/**
 * Class ViewStream
 *
 * uses Tuum/View as template renderer for ViewStream.
 * include 1.0 or later version.
 *
 * @package Tuum\Respond\Service
 */
class Tuum implements RendererInterface
{
    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @param Renderer   $renderer
     * @param ViewHelper $view
     */
    public function __construct($renderer, $view = null)
    {
        $this->renderer   = $renderer;
        $this->viewHelper = $view;
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string   $root
     * @param callable $callable
     * @return static
     */
    public static function forge($root, $callable = null)
    {
        $renderer = Renderer::forge($root);
        if (is_callable($callable)) {
            $renderer = call_user_func($callable, $renderer);
        }

        return new static($renderer, ViewHelper::forge());
    }

    /**
     * @param string $template
     * @param array  $data
     * @param array  $helper
     * @return string
     */
    public function __invoke($template, array $data, array $helper = [])
    {
        $view_data = array_merge($data, $helper);

        return $this->renderer->render($template, $view_data);
    }
}