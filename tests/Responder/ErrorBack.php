<?php
namespace tests\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ErrorViewInterface;
use Tuum\Respond\Responder\ViewData;

class ErrorBack implements ErrorViewInterface
{
    public $code;
    public $data;

    /**
     * create a stream for error view.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param int                    $status
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    public function __invoke( ServerRequestInterface $request, ResponseInterface $response, $status, $view)
    {
        $this->data = $view;
        return $response->withStatus($status);
    }
}