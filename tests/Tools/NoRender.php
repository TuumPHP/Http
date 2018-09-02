<?php
namespace tests\Tools;

use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Service\ViewHelper;

class NoRender implements RendererInterface
{
    public $template;
    public $helper;
    public $data;

    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function render($template, ViewHelper $helper, array $data = [])
    {
        $this->template = $template;
        $this->helper = $helper;
        $this->data = $data;
        
        $content = 'data:' . implode(',', $data) . 
            'view:' . implode(',', $helper->getPayload()->getData());
        
        return $content;
    }
}