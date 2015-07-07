<?php
namespace Tuum\Respond\Service;

/**
 * Interface ViewStreamInterface
 *
 * a stream for view template.
 *
 * @package Tuum\Application\Service
 */
interface ViewStreamInterface
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
     * renders the view.
     *
     * @return string
     */
    public function render();

    /**
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return string
     */
    public function renderView($view_file, $data = null);
}