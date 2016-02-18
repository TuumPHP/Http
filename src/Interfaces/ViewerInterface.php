<?php
namespace Tuum\Respond\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ViewStreamInterface
 *
 * for rendering a template file ($viewFile) with data ($viewData).
 *
 * Current (as of 2.x-dev) the $viewData can be either of:
 * ViewDataInterface or array.
 *
 * @package Tuum\Application\Service
 */
interface ViewerInterface
{
    /**
     * renders $viewFile template file with $viewData data.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param string                  $viewFile
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewFile, $viewData);
}