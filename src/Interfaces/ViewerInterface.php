<?php
namespace Tuum\Respond\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\ViewData;

/**
 * Interface ViewStreamInterface
 *
 * a stream for view template.
 *
 * @package Tuum\Application\Service
 */
interface ViewerInterface 
{
    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $view_file
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    public function __invoke( ServerRequestInterface $request, ResponseInterface $response, $view_file, $view);
}