<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Interfaces\PresenterInterface;
use Zend\Diactoros\UploadedFile;

class UploadController
{
    /**
     * @var PresenterInterface
     */
    private $viewer;

    /**
     * @var Responder
     */
    private $responder;
    
    /**
     * UploadController constructor.
     *
     * @param PresenterInterface $viewer
     */
    private function __construct($viewer)
    {
        $this->viewer = $viewer;
    }

    /**
     * factory for this class.
     *
     * @param Dispatcher $app
     * @return UploadController
     */
    public static function forge($app)
    {
        $viewer = UploadViewer::forge($app);
        $self =  new self($viewer);
        $self->responder = $app->get('responder');
        return $self;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $method = $request->getMethod()==='POST' ? 'onPost' : 'onGet';
        return $this->$method($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function onGet(ServerRequestInterface $request, ResponseInterface $response)
    {
        $view = $this->responder->getViewData();
        return $this->responder->view($request, $response)
            ->call($this->viewer, $view);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function onPost(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var UploadedFile $upload */
        $uploaded = $request->getUploadedFiles();
        $upload   = $uploaded['up'][0];
        $view = $this->responder->getViewData()
            ->setData('isUploaded', true)
            ->setData('dump', print_r($uploaded, true))
            ->setData('upload', $upload);
        $this->setUpMessage($view, $upload);
        return $this->responder->view($request, $response)
            ->call( [$this->viewer, '__invoke' ], $view); // callable
    }

    /**
     * @param ViewDataInterface $view
     * @param UploadedFile $upload
     */
    private function setUpMessage($view, $upload)
    {
        if ($upload->getError()===UPLOAD_ERR_NO_FILE) {
            $view->setError('please uploaded a file');
        } elseif ($upload->getError()===UPLOAD_ERR_FORM_SIZE || $upload->getError()===UPLOAD_ERR_INI_SIZE) {
            $view->setError('uploaded file size too large!');
        } elseif ($upload->getError()!==UPLOAD_ERR_OK) {
            $view->setError('uploading failed!');
        } else {
            $view->setError('uploaded a file');
        }
    }
}