<?php
namespace tests\Responder;

use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewerInterface;

class LocalView implements ViewerInterface
{
    /**
     * @var string
     */
    public $view_file;

    /**
     * @var ViewData
     */
    public $data;

    /**
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return string
     */
    public function withView($view_file, $data = null)
    {
        $this->view_file = $view_file;
        $this->data      = $data;
        return $this;
    }
}