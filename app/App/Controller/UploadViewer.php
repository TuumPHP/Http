<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\PresenterTrait;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Responder;

class UploadViewer implements PresenterInterface
{
    use PresenterTrait;

    /**
     * @param Responder $responder
     */
    public function __construct($responder)
    {
        $this->setResponder($responder);
    }

    /**
     * @return ResponseInterface
     */
    protected function onGet()
    {
        $this->getViewData()
            ->setSuccess('Please upload a file (max 512 byte). ')
            ->setData('isUploaded', false);
        return $this->view()->render('upload');
    }

    /**
     * @return ResponseInterface
     */
    protected function onPost()
    {
        $this->setUpMessage();

        return $this->view()->render('upload');
    }

    /**
     * 
     */
    private function setUpMessage()
    {
        /** @var UploadedFileInterface $upload */
        $viewData = $this->getViewData();
        $upload = $viewData->getData()['upload'];
        $error_code = $upload->getError();

        if ($error_code === UPLOAD_ERR_NO_FILE) {
            $viewData->setError('please uploaded a file');
        } elseif ($error_code === UPLOAD_ERR_FORM_SIZE) {
            $viewData->setError('uploaded file size too large!');
        } elseif ($error_code === UPLOAD_ERR_INI_SIZE) {
            $viewData->setError('uploaded file size too large!');
        } elseif ($error_code !== UPLOAD_ERR_OK) {
            $viewData->setError('uploading failed!');
        } else {
            $viewData->setSuccess('uploaded a file');
        }
        $viewData
            ->setData('isUploaded', true)
            ->setData('dump', print_r($upload, true))
            ->setData('upload', $upload)
            ->setData('error_code', $error_code);
    }
}