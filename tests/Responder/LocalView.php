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
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        $this->view_file = $view->getViewFile();
        $this->data      = $view;
        $response = $response->withHeader('ViewFile', $view->getViewFile());
        $response->getBody()->write(implode(',',$view->getData()));
        return $response;
    }
}