<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Controller\ResponderHelperTrait;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Responder;

class UploadController
{
    use DispatchByMethodTrait;

    use ResponderHelperTrait;

    /**
     * @var PresenterInterface
     */
    private $viewer;

    /**
     * UploadController constructor.
     *
     * @param PresenterInterface $viewer
     * @param Responder          $responder
     */
    public function __construct($viewer, $responder)
    {
        $this->viewer = $viewer;
        $this->responder = $responder;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->dispatch($request, $response);
    }

    /**
     * @return ResponseInterface
     */
    public function onGet()
    {
        return $this->call([$this->viewer, 'onGet']);
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        /** @var UploadedFileInterface $upload */
        $uploaded = $this->getRequest()->getUploadedFiles();
        $upload   = $uploaded['up'][0];
        return $this->call([$this->viewer, 'onPost'], [
            'upload' => $upload,
        ]);
    }
}