<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorView implements ErrorViewInterface
{
    /**
     * @var ViewerInterface
     */
    private $view;

    /**
     * @var string
     */
    public $default_error = '';

    /**
     * @var array
     */
    public $statusView = [];

    /**
     * @var
     */
    private $exitOnTerminate = true;

    /**
     * @param ViewerInterface $viewStream
     */
    public function __construct(ViewerInterface $viewStream)
    {
        $this->view = $viewStream;
    }

    /**
     * @param ViewerInterface $viewStream
     * @param array           $options
     * @return static
     */
    public static function forge(
        ViewerInterface $viewStream,
        array $options
    ) {
        $error = new static($viewStream);
        $options += [
            'default' => null,
            'status'  => [],
        ];
        $error->default_error = $options['default'];
        $error->statusView    = $options['status'];

        return $error;
    }

    /**
     * @param int $status
     * @return string
     */
    private function findViewFromStatus($status)
    {
        $status = (string)$status;

        return isset($this->statusView[$status]) ?
            $this->statusView[$status] :
            $this->default_error;
    }

    /**
     * create a response for error view.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $data
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $data)
    {
        $status = $data->getStatus();
        $data->setViewFile($this->findViewFromStatus($status));

        return $this->view->withView($request, $response, $data);
    }

    /**
     * @param bool $exitOnTerminate
     */
    public function setExitOnTerminate($exitOnTerminate)
    {
        $this->exitOnTerminate = $exitOnTerminate;
    }
}