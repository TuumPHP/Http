<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Interfaces\PresenterInterface;

class UploadViewer implements PresenterInterface
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @param Dispatcher $app
     * @return UploadViewer
     */
    public static function forge($app)
    {
        $self            = new self;
        $self->responder = $app->get('responder');
        return $self;
    }

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewData)
    {
        $data = $viewData->getData();
        if (!isset($data['isUploaded']) || !$data['isUploaded']) {
            $viewData->setSuccess('Please upload a file (max 512 byte). ');
        } else {
            $viewData = $this->setUpMessage($viewData);
        }
        return $this->responder->view($request, $response)
            ->withView($viewData)
            ->render('upload');
    }

    /**
     * @param ViewDataInterface $viewData
     * @return ViewDataInterface
     */
    private function setUpMessage($viewData)
    {
        $data       = $viewData->getData();
        $error_code = isset($data['error_code']) ? $data['error_code'] : null;
        if (!$error_code) {
            return $viewData;
        }
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
        return $viewData;
    }
}