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
        $this->responder = $responder;
    }

    /**
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    protected function onGet($viewData)
    {
        $viewData
            ->setSuccess('Please upload a file (max 512 byte). ')
            ->setData('isUploaded', false);
        return $this->view($viewData)->render('upload');
    }

    /**
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    protected function onPost($viewData)
    {
        $data = $viewData->getData();
        $viewData = $this->setUpMessage($viewData, $data['upload']);

        return $this->view($viewData)->render('upload');
    }

    /**
     * @param ViewDataInterface $viewData
     * @param UploadedFileInterface      $upload
     * @return ViewDataInterface
     */
    private function setUpMessage($viewData, $upload)
    {
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
            $viewData->setError('uploaded a file');
        }
        $viewData
            ->setData('isUploaded', true)
            ->setData('dump', print_r($upload, true))
            ->setData('upload', $upload)
            ->setData('error_code', $error_code);
        return $viewData;
    }
}