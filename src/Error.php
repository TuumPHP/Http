<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Http\Service\ErrorViewInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class Error
{
    const UNAUTHORIZED = 401;
    const ACCESS_DENIED = 403;
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
        if ($input instanceof StreamInterface) {
            $stream = $input;

        } elseif (is_string($input)) {
            $stream = new Stream('php://memory', 'wb+');
            $stream->write($input);

        } elseif (is_resource($input)) {
            $stream = $input;

        } elseif (is_object($input) && method_exists($input, '__toString')) {
            $stream = new Stream('php://memory', 'wb+');
            $stream->write($input->__toString());

        } else {
            throw new \InvalidArgumentException;
        }

        return new Response(
            $stream,
            $status,
            $header
        );
    }

    /**
     * @param int   $status
     * @param array $data
     * @return ResponseInterface
     */
    public function json($status, $data)
    {
        $stream = json_encode($data);
        return $this->respond($stream, $status, ['Content-Type' => 'application/json']);
    }

    /**
     * @param int   $status
     * @param array $data
     * @return ResponseInterface
     */
    public function view($status, $data = [])
    {
        /** @var ErrorViewInterface $view */
        if (!$view = RequestHelper::getApp($this->request)->get(ErrorViewInterface::class)) {
            throw new \BadMethodCallException;
        }
        return $view->respond($status, $data);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function unauthorized($data = [])
    {
        return $this->view(self::UNAUTHORIZED, $data);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function forbidden($data = [])
    {
        return $this->view(self::ACCESS_DENIED, $data);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    public function notFound($data = [])
    {
        return $this->view(self::FILE_NOT_FOUND, $data);
    }
}
