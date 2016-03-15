<?php

use App\App\Dispatcher;
use App\App\DocumentMap;
use App\App\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

return function (Dispatcher $app, Responder $responder) {

    /**
     * for top page /
     */
    $app->add('/',
        function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
            $viewData = $responder->getViewData();
            if (!$responder->session()->get('first.time')) {
                $viewData->setSuccess('Thanks for downloading Tuum/Respond.');
                $responder->session()->set('first.time', true);
            }
            return $responder->view($request, $response)
                ->render('index', $viewData);
        });

    $app->add('/login',
        function (ServerRequestInterface $request, ResponseInterface $response) use($responder) {
            $post = $request->getParsedBody();
            $viewData = $responder->getViewData();
            if (isset($post['logout'])) {
                $responder->session()->set('login.name', null);
                $viewData->setSuccess('logged out');
            }
            elseif (isset($post['login'])) {
                if ($post['login']) {
                    $responder->session()->set('login.name', $post['login']);
                    $viewData->setSuccess('logged as:' . $post['login']); // XSS!!!
                    return $responder->redirect($request, $response)->toPath('/', $viewData);
                }
                $viewData->setAlert('enter login name');
            }
            return $responder->redirect($request, $response)->toPath('/', $viewData);
        });

    /**
     * for displaying form for /jump
     */
    $app->add('/jump',
        function ($request, $response) use ($responder) {
            $viewData = $responder->getViewData()
                ->setSuccess('try jump to another URL. ');
            return $responder->view($request, $response)
                ->render('jump', $viewData);
        });

    $app->add('/jumper',
        function (ServerRequestInterface $request, $response) use ($responder) {
            $viewData = $responder->getViewData()
                ->setError('redirected back!')
                ->setInputData($request->getParsedBody())
                ->setInputErrors([
                    'jumped' => 'redirected error message',
                    'date' => 'your date',
                    'gender' => 'your gender',
                    'movie' => 'selected movie',
                    'happy' => 'be happy!'
                ]);

            return $responder->redirect($request, $response)
                ->toPath('jump', $viewData);
        });

    /**
     * file upload sample, /upload.
     */
    $app->add('/upload', UploadController::class);

    /**
     * for other samples
     */
    $app->add('/content',
        function (ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)
                ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>');
        });

    $app->add('/objGraph',
        function (ServerRequestInterface $request) {
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
        function (ServerRequestInterface $request, $response) {
            $viewData = Respond::getViewData($request)
                ->setData([
                    'text' => 'this is text-value',
                    'date' => date('Y-m-d'),
                ]);
            return Respond::view($request, $response)->render('forms', $viewData);
        });

    $app->add('/docs/(?P<pathInfo>.*)', DocumentMap::class);
    
    return $app;
};