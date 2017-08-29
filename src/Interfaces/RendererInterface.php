<?php
namespace Tuum\Respond\Interfaces;

use Tuum\Respond\Service\ViewHelper;

/**
 * Interface RendererInterface
 * 
 * for rendering a template file ($template) with data ($data)
 *
 * @package Tuum\Respond\Interfaces
 */
interface RendererInterface
{
    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function __invoke($template, ViewHelper $helper, array $data = []);
}