<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Zend\Diactoros\UploadedFile;

/**
 * set routes and dispatch.
 *
 * @param ServerRequestInterface $request
 * @return ResponseInterface
 */
return function ($request) {

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $all = function ($request) {
        return Respond::view($request)
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
            ->withMessage('redirected back!')
            ->withInputData(['jumped' => 'redirected text'])
            ->withInputErrors(['jumped' => 'redirected error message'])
            ->toPath('jump');
    };

    /**
     * file upload sample.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    $up = function(ServerRequestInterface $request) {

        $responder = Respond::view($request);
        if ($request->getMethod()==='POST') {

            $uploaded = $request->getUploadedFiles();
            $responder
                ->with('isUploaded', true)
                ->with('dump', print_r($uploaded, true));
            /** @var UploadedFile $upload */
            $upload = $uploaded['up'][0];
            $responder->with('upload', $upload);

            if ($upload->getError()===UPLOAD_ERR_NO_FILE) {
                $responder->withErrorMsg('please uploaded a file');
            } elseif ($upload->getError()===UPLOAD_ERR_FORM_SIZE || $upload->getError()===UPLOAD_ERR_INI_SIZE) {
                $responder->withErrorMsg('uploaded file size too large!');
            } elseif ($upload->getError()!==UPLOAD_ERR_OK) {
                $responder->withErrorMsg('uploading failed!');
            } else {
                $responder->withMessage('uploaded a file');
            }
        }
        return $responder
            ->asView('upload');
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
        '/throw'  => $throw,
        '/upload' => $up,
        '/'       => $all,
    );

    /**
     * main routine: route match!!!
     */
    $response = null;
    $path     = RequestHelper::getPathInfo($request);
    foreach ($routes as $root => $app) {
        if ($root === $path) {
            $response = $app($request);
            break;
        }
    }
    return $response;
};
