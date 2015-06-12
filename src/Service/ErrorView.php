<?php
namespace Tuum\Http\Service;

use Exception;
use Psr\Http\Message\StreamInterface;
use Tuum\Http\ResponseHelper;

class ErrorView implements ErrorViewInterface
{
    /**
     * @var ViewStreamInterface
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
     * @param ViewStreamInterface $viewStream
     */
    public function __construct(ViewStreamInterface $viewStream)
    {
        $this->view = $viewStream;
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
     * error handler when catching an exception.
     * renders an error page with exception's code.
     *
     * @param Exception $e
     */
    public function __invoke($e)
    {
        $code     = $e->getCode() ?: 500;
        $stream   = $this->view->withView($this->findViewFromStatus($code));
        $response = ResponseHelper::createResponse($stream, $code);
        ResponseHelper::emit($response);
        exit;
    }

    /**
     * @param int   $code
     * @param array $data
     * @return StreamInterface
     */
    public function getStream($code, $data = [])
    {
        return $this->view->withView($this->findViewFromStatus($code), $data);
    }
}