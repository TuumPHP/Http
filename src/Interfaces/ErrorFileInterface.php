<?php
namespace Tuum\Respond\Interfaces;

interface ErrorFileInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param int   $status
     * @return string
     */
    public function find($status);
}