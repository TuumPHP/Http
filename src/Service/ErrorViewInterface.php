<?php
namespace Tuum\Http\Service;

use Exception;
use Psr\Http\Message\ResponseInterface;

interface ErrorViewInterface
{
    /**
     * error handler when catching an exception.
     * returns a response with error page.
     *
     * @param Exception $e
     * @return ResponseInterface
     */
    public function __invoke($e);

    /**
     * @param int   $code
     * @param array $data
     * @return ResponseInterface
     */
    public function respond($code, $data = []);

}