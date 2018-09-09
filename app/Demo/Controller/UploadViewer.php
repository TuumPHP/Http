<?php
namespace App\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\AbstractPresenter;
use Tuum\Respond\Responder;

class UploadViewer extends AbstractPresenter
{
    /**
     * @param Responder $responder
     */
    public function __construct($responder)
    {
        $this->setResponder($responder);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    protected function html(array $data)
    {
        if (array_key_exists('upload', $data)) {
            $uploadedFile = $data['upload'];
            $this->setUpMessage($uploadedFile);
        } else {
            $this->getPayload()
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
        $viewData = $this->getPayload();
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