<?php
namespace tests\Responder;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\ViewData;

class ErrorBack implements ErrorViewInterface
{
    public $code;
    public $data;

    /**
     * create a stream for error view.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        $this->code = $view->getStatus();
        $this->data = $view;
        return $response->withStatus($this->code);
    }
}