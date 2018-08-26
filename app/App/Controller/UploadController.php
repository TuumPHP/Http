<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\AbstractController;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Responder;

class UploadController extends AbstractController
{
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
        $this->setResponder($responder);
    }
    
    /**
     * @return ResponseInterface
     */
    public function onGet()
    {
        return $this->call(UploadViewer::class);
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        /** @var UploadedFileInterface $upload */
        $uploaded = $this->getRequest()->getUploadedFiles();
        $upload   = $uploaded['up'][0];
        return $this->call(UploadViewer::class, [
            'upload' => $upload,
        ]);
    }
}