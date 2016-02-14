<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\ViewData;

interface ErrorViewInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param int                    $status
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $status, $view);
}