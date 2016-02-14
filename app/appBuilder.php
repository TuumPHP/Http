<?php

use App\App\Dispatcher;
use App\App\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\ViewData;

return function(Responder $responder) {

    $app = new Dispatcher();

    /**
     * for top page /
     */
    $app->add('/',
        function (ServerRequestInterface $request, ResponseInterface $response) use($responder) {
            return $responder->view($request, $response)
                ->asView('index');
        });

    /**
     * for displaying form for /jump
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    $presentJump = function (ServerRequestInterface $request, ResponseInterface $response, $view) {
        return Respond::view($request, $response)
            ->asView('jump', $view);
    };

    $app->add('/jump', function ($request, $response) use($presentJump) {
        $view = Respond::getResponder($request)->getViewData();
        return Respond::view($request, $response)->call($presentJump, $view);
    });

    $app->add('/jumper',
        function(ServerRequestInterface $request, ResponseInterface $response) {
            $view = Respond::getResponder($request)->getViewData();
            $view->setSuccess('redirected back!')
                ->setInputData(['jumped' => 'redirected text'])
                ->setInputErrors(['jumped' => 'redirected error message']);

            return Respond::redirect($request, $response)
                ->toPath('jump', null, $view);
        });

    $app->add('/jumped',
        function ($request, $response) use($presentJump) {
            $view = Respond::getResponder($request)->getViewData()
                ->setSuccess('redrawn form!')
                ->setInputData(['jumped' => 'redrawn text'])
                ->setInputErrors(['jumped' => 'redrawn error message']);
            return Respond::view($request, $response)->call($presentJump, $view);
        });

    /**
     * file upload sample, /upload.
     */
    $app->add('/upload', UploadController::class);

    /**
     * for other samples
     */
    $app->add('/content',
        function(ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)
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
        function(ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)->asView('forms');
        });

    return $app;
};