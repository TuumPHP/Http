<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\ViewData;
use Zend\Diactoros\UploadedFile;
use Koriym\Printo\Printo;

/**
 * set routes and dispatch.
 *
 * @param ServerRequestInterface $request
 * @param Responder              $responder
 * @return ResponseInterface
 */
return function ($request, $responder) {

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $all = function ($request) use($responder) {
        return $responder->view($request)
            ->asView('index');
    };

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $jump = function ($request) {
        return Respond::view($request)
            ->asView('jump');
    };

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $jumper = function ($request) {
        return Respond::redirect($request)
            ->withSuccess('redirected back!')
            ->withInputData(['jumped' => 'redirected text'])
            ->withInputErrors(['jumped' => 'redirected error message'])
            ->toPath('jump');
    };

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $jumped = function ($request) use($jump) {
        $request = Respond::withViewData($request, function(ViewData $view) {
            $view->setSuccess('redrawn form!');
            $view->setInputData(['jumped' => 'redrawn text']);
            $view->setInputErrors(['jumped' => 'redrawn error message']);
            return $view;
        });
        return $jump($request);
    };

    /**
     * file upload sample.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $up = function(ServerRequestInterface $request) {

        if ($request->getMethod()==='POST') {

            $request = Respond::withViewData($request, function(ViewData $view) use($request) {

                /** @var UploadedFile $upload */
                $uploaded = $request->getUploadedFiles();
                $upload   = $uploaded['up'][0];
                $view
                    ->setData('isUploaded', true)
                    ->setData('dump', print_r($uploaded, true))
                    ->setData('upload', $upload);

                if ($upload->getError()===UPLOAD_ERR_NO_FILE) {
                    $view->setError('please uploaded a file');
                } elseif ($upload->getError()===UPLOAD_ERR_FORM_SIZE || $upload->getError()===UPLOAD_ERR_INI_SIZE) {
                    $view->setError('uploaded file size too large!');
                } elseif ($upload->getError()!==UPLOAD_ERR_OK) {
                    $view->setError('uploading failed!');
                } else {
                    $view->setError('uploaded a file');
                }
                return $view;
            });
        }
        return Respond::view($request)
            ->asView('upload');
    };
    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $content = function(ServerRequestInterface $request) {
        return Respond::view($request)
            ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>');
    };

    $objGraph = function(ServerRequestInterface $request) {
        echo (new Printo(Respond::getResponder($request)));
        exit;
    };

    /**
     * @throw \Exception
     */
    $throw = function () {
        throw new \Exception('always throws exception');
    };

    /**
     * $routes to aggregate all the routes.
     */
    $routes = array(
        '/jump'   => $jump,
        '/jumper' => $jumper,
        '/jumped' => $jumped,
        '/throw'  => $throw,
        '/upload' => $up,
        '/content'=> $content,
        '/objGraph' => $objGraph,
        '/'       => $all,
    );

    /**
     * main routine: route match!!!
     */
    $response = null;
    $path     = ReqAttr::getPathInfo($request);
    foreach ($routes as $root => $app) {
        if ($root === $path) {
            $response = $app($request);
            break;
        }
    }
    return $response;
};
