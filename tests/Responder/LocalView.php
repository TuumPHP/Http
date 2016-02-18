<?php
namespace tests\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Interfaces\ViewerInterface;

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
     * @param mixed|ViewData         $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $view_file, $viewData)
    {
        $this->view_file = $view_file;
        $this->data      = $viewData;
        $response        = $response->withHeader('ViewFile', $view_file);
        $response->getBody()->write(implode(',', $viewData ? $viewData->getData() : []));
        return $response;
    }
}