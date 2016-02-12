<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\PresenterInterface;

class UploadViewer implements PresenterInterface
{
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
        return Respond::view($request)
            ->withViewData(function() use($view) {
                return $view; // must use the passed $view. 
            })
            ->asView('upload');
    }
}