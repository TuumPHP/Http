<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\PresentByContentTrait;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Responder;

class UploadViewer implements PresenterInterface
{
    use PresentByContentTrait;

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
    protected function html()
    {
        $data = $this->getViewData()->getData();
        if (array_key_exists('upload', $data)) {
            $uploadedFile = $data['upload'];
            $this->setUpMessage($uploadedFile);
        } else {
            $this->getViewData()
                 ->setSuccess('Please upload a file (max 512 byte). ')
                 ->setData('isUploaded', false);
        }
        return $this->view()->render('upload');
    }

    /**
     * @param UploadedFileInterface $upload
     */
    private function setUpMessage($upload)
    {
        $viewData = $this->getViewData();
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