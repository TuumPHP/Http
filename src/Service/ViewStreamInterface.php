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
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return ViewStreamInterface
     */
    public function withView($view_file, $data = null);

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
    public function modRenderer($modifier);
}