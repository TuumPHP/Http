<?php
namespace Tuum\Http\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Http\RequestHelper;
use Tuum\Http\ResponseHelper;
use Tuum\Http\Service\ErrorViewInterface;

class Error
{
    const UNAUTHORIZED   = 401;
    const ACCESS_DENIED  = 403;
    const FILE_NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $data = [];

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->data    = $this->request->getAttributes();
    }

    /**
     * @param ServerRequestInterface $request
     * @return static
     */
    public static function forge(ServerRequestInterface $request)
    {
        return new static($request);
    }

    // +----------------------------------------------------------------------+
    //  methods for saving data for response.
    // +----------------------------------------------------------------------+
    /**
     * creates a generic response.
     *
     * @param string|StreamInterface|resource $input
     * @param int                             $status
     * @param array                           $header
     * @return ResponseInterface
     */
    public function respond($input, $status = self::INTERNAL_ERROR, array $header = [])
    {
        return ResponseHelper::createResponse($input, $status, $header);
    }

    /**
     * @param int   $status
     * @param array $data
     * @return ResponseInterface
     */
    public function asJson($status, $data)
    {
        $stream = json_encode($data);
        return $this->respond($stream, $status, ['Content-Type' => 'application/json']);
    }

    /**
     * @param int   $status
     * @param array $data
     * @return ResponseInterface
     */
    public function asView($status, $data = [])
    {
        /** @var ErrorViewInterface $view */
        if (!$view = RequestHelper::getService($this->request, ErrorViewInterface::class)) {
            throw new \BadMethodCallException;
        }
        $stream = $view->getStream($status, $data);
        return $this->respond($stream, $status);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function unauthorized($data = [])
    {
        return $this->asView(self::UNAUTHORIZED, $data);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function forbidden($data = [])
    {
        return $this->asView(self::ACCESS_DENIED, $data);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function notFound($data = [])
    {
        return $this->asView(self::FILE_NOT_FOUND, $data);
    }
}
