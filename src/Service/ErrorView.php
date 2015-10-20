<?php
namespace Tuum\Respond\Service;

use Exception;
use Tuum\Respond\ResponseHelper;

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
     * @var
     */
    private $exitOnTerminate = true;

    /**
     * @param ViewStreamInterface $viewStream
     */
    public function __construct(ViewStreamInterface $viewStream)
    {
        $this->view = $viewStream;
    }

    /**
     * @param ViewStreamInterface $viewStream
     * @param array               $options
     * @return static
     */
    public static function forge(
        ViewStreamInterface $viewStream,
        array $options
    ) {
        $error = new static($viewStream);
        $options += [
            'default' => null,
            'status'  => [],
            'handler' => false,
        ];
        $error->default_error = $options['default'];
        $error->statusView = $options['status'];
        set_exception_handler($error); // catching an uncaught exception!!!

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
        $this->terminate();
    }

    private function terminate()
    {
        if ($this->exitOnTerminate) {
            exit;
        }
        return;
    }

    /**
     * @param int   $code
     * @param array $data
     * @return string
     */
    public function getStream($code, $data = [])
    {
        return $this->view->withView($this->findViewFromStatus($code), $data);
    }

    /**
     * @param bool $exitOnTerminate
     */
    public function setExitOnTerminate($exitOnTerminate)
    {
        $this->exitOnTerminate = $exitOnTerminate;
    }
}