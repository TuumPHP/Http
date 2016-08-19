<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Controller\ResponderHelperTrait;
use Tuum\Respond\Interfaces\PresenterInterface;

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
        $viewer          = UploadViewer::forge($app);
        $self            = new self($viewer);
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