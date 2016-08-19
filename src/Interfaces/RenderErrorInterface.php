<?php
namespace Tuum\Respond\Interfaces;

interface RenderErrorInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param int   $status
     * @param array $data
     * @param array $helper
     * @return string
     */
    public function __invoke($status, array $data, array $helper = []);
}