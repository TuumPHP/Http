<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\PresenterInterface;

class UploadViewer implements PresenterInterface
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @param Dispatcher $app
     * @return UploadViewer
     */
    public static function forge($app)
    {
        $self = new self;
        $self->responder = $app->get('responder');
        return $self;
    }
    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        return $this->responder->view($request, $response)
            ->asView('upload', $view);
    }
}