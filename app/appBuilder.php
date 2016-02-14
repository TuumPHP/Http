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
        function (ServerRequestInterface $request) use($responder) {
            return $responder->view($request)
                ->asView('index');
        });

    /** @noinspection PhpUnusedParameterInspection */
    /**
     * for displaying form for /jump
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $res
     * @param mixed|ViewData         $view
     * @return ResponseInterface
     */
    $presentJump = function (ServerRequestInterface $request, $res, $view) {
        return Respond::view($request)
            ->asView('jump', $view);
    };

    $app->add('/jump', function ($request) use($presentJump) {
        $view = Respond::getResponder($request)->getViewData();
        return Respond::view($request)->call($presentJump, $view);
    });

    $app->add('/jumper',
        function(ServerRequestInterface $request) {
            $view = Respond::getResponder($request)->getViewData();
            $view->setSuccess('redirected back!')
                ->setInputData(['jumped' => 'redirected text'])
                ->setInputErrors(['jumped' => 'redirected error message']);

            return Respond::redirect($request)
                ->toPath('jump', null, $view);
        });

    $app->add('/jumped',
        function ($request) use($presentJump) {
            $view = Respond::getResponder($request)->getViewData()
                ->setSuccess('redrawn form!')
                ->setInputData(['jumped' => 'redrawn text'])
                ->setInputErrors(['jumped' => 'redrawn error message']);
            return Respond::view($request)->call($presentJump, $view);
        });

    /**
     * file upload sample, /upload.
     */
    $app->add('/upload', UploadController::class);

    /**
     * for other samples
     */
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

    return $app;
};