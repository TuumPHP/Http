<?php
namespace Tuum\Http\Service;

use Exception;
use Psr\Http\Message\StreamInterface;

interface ErrorViewInterface
{
    /**
     * error handler when catching an exception.
     * renders an error page with exception's code.
     *
     * @param Exception $e
     */
    public function __invoke($e);

    /**
     * create a stream for error view.
     *
     * @param int   $code
     * @param array $data
     * @return StreamInterface
     */
    public function getStream($code, $data = []);

}