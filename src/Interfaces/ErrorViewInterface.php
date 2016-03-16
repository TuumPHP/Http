<?php
namespace Tuum\Respond\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorViewInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param int                     $status
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $status, $viewData);
}