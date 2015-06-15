<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\StreamInterface;

/**
 * Interface ViewStreamInterface
 *
 * a stream for view template.
 *
 * @package Tuum\Application\Service
 */
interface ViewStreamInterface extends StreamInterface
{
    /**
     * sets view template file and data to be rendered.
     *
     * @param string    $view_file
     * @param ViewData  $data
     * @return ViewStreamInterface
     */
    public function withView($view_file, $data = null);

    /**
     * sets view contents to be rendered.
     *
     * @param string    $content
     * @param ViewData  $data
     * @return ViewStreamInterface
     */
    public function withContent($content, $data = null);

    /**
     * modifies the internal renderer's setting.
     *
     * $modifier = function($renderer) {
     *    // modify the renderer.
     * }
     *
     * @param \Closure $modifier
     */
    public function modRenderer($modifier);
}