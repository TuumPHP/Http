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
        if (isset($options['default'])) {
            $error->default_error = $options['default'];
        }
        if (isset($options['status'])) {
            $error->statusView = $options['status'];
        }
        if (isset($options['handler']) && $options['handler']) {
            set_exception_handler($error); // catch uncaught exception!!!
        }

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
        exit;
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
}