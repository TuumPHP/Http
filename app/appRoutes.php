<?php

use App\App\Controller\ForbiddenController;
use App\App\Dispatcher;
use App\App\DocumentMap;
use App\App\Controller\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

return function (Dispatcher $app) {

    /** @var Responder $responder */
    $responder = $app->get(Responder::class);
    
    /**
     * for top page /
     */
    $app->add('/',
        function (ServerRequestInterface $request) use ($responder) {
            if (!$responder->session()->get('first.time')) {
                $responder->session()->set('first.time', true);
                $responder->getPayload($request)
                    ->setSuccess('Thanks for downloading Tuum/Respond.');
            }
            return $responder
                ->view($request)
                ->render('index');
        });
    
    $app->add('/info', function (ServerRequestInterface $request) use($responder) {
        return $responder->view($request)
            ->asObContents(function() {
                phpinfo();
            });
    });

    $app->add('/login',
        function (ServerRequestInterface $request) use($responder) {
            $post = $request->getParsedBody();
            $view = $responder->getPayload($request);
            if (isset($post['logout'])) {
                $responder->session()->set('login.name', null);
                $view->setSuccess('logged out');
            }
            elseif (isset($post['login'])) {
                if ($post['login']) {
                    $responder->session()->set('login.name', $post['login']);
                    $view->setSuccess('logged as: ' . $post['login']);
                } else {
                    $view->setAlert('enter login name');
                }
            }
            return $responder
                ->redirect($request)
                ->toPath('/');
        });

    /**
     * for displaying form for /jump
     */
    $app->add('/jump', \App\App\Controller\JumpController::class);

    /**
     * file upload sample, /upload.
     */
    $app->add('/upload', UploadController::class);

    /**
     * for other samples
     */
    $app->add('/content',
        function (ServerRequestInterface $request) {
            return Respond::view($request)
                ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>');
        });

    $app->add('/objGraph',
        function (ServerRequestInterface $request) {
            return Respond::view($request)
                ->asContents((new Printo(Respond::getResponder())));
        });

    /**
     * @throw \Exception
     */
    $app->add('/throw',
        function () {
            throw new \Exception('always throws exception');
        });

    $app->add('/forbidden', ForbiddenController::class);

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $app->add('/forms',
        function (ServerRequestInterface $request) {
            Respond::getPayload($request)
                ->setData([
                    'text' => 'this is text-value',
                    'date' => date('Y-m-d'),
                ]);
            return Respond::view($request)->render('forms');
        });

    $app->add('/docs/(?P<pathInfo>.*)', DocumentMap::class);
    
    return $app;
};