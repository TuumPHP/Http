<?php

use App\App\Dispatcher;
use App\App\UploadController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\ViewData;
use Koriym\Printo\Printo;

/**
 * set routes and dispatch.
 *
 * @param ServerRequestInterface $request
 * @param Responder              $responder
 * @return ResponseInterface
 */
return function ($request, $responder) {

    $app = new Dispatcher();

    $app->add('/',
        function (ServerRequestInterface $request) use($responder) {
            return $responder->view($request)
                ->asView('index');
        });

    /**
     * for displaying form for /jump
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $presentJump = function (ServerRequestInterface $request) {
        return Respond::view($request)
            ->asView('jump');
    };

    $app->add('/jump', $presentJump);

    $app->add('/jumper',
        function(ServerRequestInterface $request) {
            return Respond::redirect($request)
                ->withSuccess('redirected back!')
                ->withInputData(['jumped' => 'redirected text'])
                ->withInputErrors(['jumped' => 'redirected error message'])
                ->toPath('jump');
        });

    $app->add('/jumped',
        function ($request) use($presentJump) {
            $request = Respond::withViewData($request, function(ViewData $view) {
                $view->setSuccess('redrawn form!');
                $view->setInputData(['jumped' => 'redrawn text']);
                $view->setInputErrors(['jumped' => 'redrawn error message']);
                return $view;
            });
            return Respond::presenter($request)->call($presentJump);
        });

    /**
     * file upload sample.
     */
    $app->add('/upload', UploadController::class);

    $app->add('/content',
        function(ServerRequestInterface $request) {
            return Respond::view($request)
                ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>');
        });

    $app->add('/objGraph',
        function(ServerRequestInterface $request) {
            echo (new Printo(Respond::getResponder($request)));
            exit;
        });

    /**
     * @throw \Exception
     */
    $app->add('/throw',
        function () {
            throw new \Exception('always throws exception');
        });

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $app->add('/forms',
        function(ServerRequestInterface $request) {
            return Respond::view($request)->asView('forms');
        });

    return $app->run($request);

};
