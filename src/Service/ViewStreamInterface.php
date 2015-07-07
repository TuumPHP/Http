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
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return string
     */
    public function renderView($view_file, $data = null);
}