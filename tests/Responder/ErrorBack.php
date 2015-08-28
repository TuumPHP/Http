<?php
namespace tests\Responder;

use Exception;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\ViewData;

class ErrorBack implements ErrorViewInterface
{
    public $code;
    public $data;

    /**
     * error handler when catching an exception.
     * renders an error page with exception's code.
     *
     * @param Exception $e
     */
    public function __invoke($e)
    {
    }

    /**
     * create a stream for error view.
     *
     * @param int            $code
     * @param array|ViewData $data
     * @return StreamInterface
     */
    public function getStream($code, $data = [])
    {
        $this->code = $code;
        $this->data = $data;
        return 'code:'. $code;
    }
}