<?php
namespace Tuum\Respond\Interfaces;

interface ErrorViewInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param int                     $status
     * @param mixed|ViewDataInterface $viewData
     * @return string
     */
    public function __invoke($status, $viewData);
}