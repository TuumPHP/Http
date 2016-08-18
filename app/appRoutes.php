<?php

use App\App\Dispatcher;
use App\App\DocumentMap;
use App\App\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

return function (Dispatcher $app, Responder $responder) {

    /**
     * for top page /
     */
    $app->add('/',
        function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
            if (!$responder->session()->get('first.time')) {
                $responder->withView(function(ViewDataInterface $view) {
                    return $view->setSuccess('Thanks for downloading Tuum/Respond.');
                });
                $responder->session()->set('first.time', true);
            }
            return $responder->view($request, $response)
                ->render('index');
        });

    $app->add('/login',
        function (ServerRequestInterface $request, ResponseInterface $response) use($responder) {
            $post = $request->getParsedBody();
            $viewData = $responder->getViewData();
            if (isset($post['logout'])) {
                $responder->session()->set('login.name', null);
                $responder->withView(function(ViewDataInterface $view) {
                    return $view->setSuccess('logged out');
                });
            }
            elseif (isset($post['login'])) {
                if ($post['login']) {
                    $responder->session()->set('login.name', $post['login']);
                    $responder->withView(function(ViewDataInterface $view) {
                        return $view->setSuccess('logged as:\' . $post[\'login\']');
                    });
                    return $responder->redirect($request, $response)->toPath('/');
                }
                $responder->withView(function(ViewDataInterface $view) {
                    return $view->setAlert('enter login name');
                });
            }
            return $responder->redirect($request, $response)->toPath('/');
        });

    /**
     * for displaying form for /jump
     */
    $app->add('/jump',
        function ($request, $response) use ($responder) {
            $responder->withView(function (ViewDataInterface $viewData) {
                return $viewData->setSuccess('try jump to another URL. ')
                    ->setData('jumped', 'text in control')
                    ->setData('date', (new DateTime('now'))->format('Y-m-d'));
            });
            return $responder->view($request, $response)
                ->render('jump');
        });

    $app->add('/jumper',
        function (ServerRequestInterface $request, $response) use ($responder) {
            $responder->getViewData()
                ->setError('redirected back!')
                ->setInput($request->getParsedBody())
                ->setInputErrors([
                    'jumped' => 'redirected error message',
                    'date' => 'your date',
                    'gender' => 'your gender',
                    'movie' => 'selected movie',
                    'happy' => 'be happy!'
                ]);

            return $responder->redirect($request, $response)
                ->toPath('jump');
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
        function (ServerRequestInterface $request, $response) {
            return Respond::view($request, $response)
                ->asContents((new Printo(Respond::getResponder($request))));
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
            Respond::getViewData($request)
                ->setData([
                    'text' => 'this is text-value',
                    'date' => date('Y-m-d'),
                ]);
            return Respond::view($request, $response)->render('forms');
        });

    $app->add('/docs/(?P<pathInfo>.*)', DocumentMap::class);
    
    return $app;
};