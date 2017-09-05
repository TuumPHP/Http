<?php
namespace Tuum\Respond\Service\Renderer;

use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Service\ViewHelper;

/**
 * Class ViewStream
 *
 * uses Tuum/View as template renderer for ViewStream.
 * include 1.0 or later version.
 *
 * @package Tuum\Respond\Service
 */
class RawPhp implements RendererInterface
{
    /**
     * @var string
     */
    private $root;

    /**
     * @param string $root
     */
    public function __construct($root)
    {
        $this->root = rtrim($root, '/');
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string   $root
     * @return static
     */
    public static function forge($root)
    {
        return new static($root);
    }

    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function render($template, ViewHelper $helper, array $data = [])
    {
        $view_data = array_merge($data, ['view' => $helper]);
        $template .= '.php'; 

        ob_start();
        $contents = $this->renderOb($template, $view_data);
        ob_end_clean();
        
        return $contents;
    }
    
    /**
     * @param string $template
     * @return bool
     */
    private function getFileLocation($template)
    {
        $file = $this->root . '/' . $template;
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('cannot find template: ' . $file);
        }
        return $file;
    }

    /**
     * @param string $__template
     * @param array  $__view_data
     * @return string
     */
    private function renderOb($__template, $__view_data)
    {
        extract($__view_data);
        /** @noinspection PhpIncludeInspection */
        include($this->getFileLocation($__template));
        return ob_get_contents();
    }
}