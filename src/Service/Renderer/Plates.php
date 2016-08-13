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
     * @param string $template
     * @param array  $data
     * @return string
     */
    public function __invoke($template, array $data)
    {
        if (isset($data['view']) && $data['view'] instanceof ViewHelper) {
            $this->renderer->addData(['view' => $data['view']]);
            unset($data['view']);
        }
        return $this->renderer->render($template, $data);
    }
}