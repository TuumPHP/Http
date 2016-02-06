<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\PresenterInterface;
use Zend\Diactoros\UploadedFile;

class UploadController
{
    /**
     * @var PresenterInterface
     */
    private $viewer;

    /**
     * UploadController constructor.
     *
     * @param PresenterInterface $viewer
     */
    public function __construct($viewer)
    {
        $this->viewer = $viewer;
    }

    /**
     * @return UploadController
     */
    public static function forge()
    {
        $viewer = new UploadViewer();
        return new self($viewer);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $method = $request->getMethod()==='POST' ? 'onPost' : 'onGet';
        return $this->$method($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function onGet(ServerRequestInterface $request)
    {
        return Respond::view($request)
            ->call($this->viewer);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function onPost(ServerRequestInterface $request)
    {
        $request = Respond::withViewData($request, function(ViewData $view) use($request) {

            /** @var UploadedFile $upload */
            $uploaded = $request->getUploadedFiles();
            $upload   = $uploaded['up'][0];
            $view
                ->setData('isUploaded', true)
                ->setData('dump', print_r($uploaded, true))
                ->setData('upload', $upload);

            $this->setUpMessage($view, $upload);
            return $view;
        });

        return Respond::view($request)
            ->call([$this->viewer, 'withView']); // callable
    }

    /**
     * @param ViewData $view
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