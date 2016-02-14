<?php
namespace tests\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\ViewData;
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $view_file
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view_file, $view)
    {
        $this->view_file = $view_file;
        $this->data      = $view;
        $response = $response->withHeader('ViewFile', $view_file);
        $response->getBody()->write(implode(',', $view ? $view->getData():[]));
        return $response;
    }
}