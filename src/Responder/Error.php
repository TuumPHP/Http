<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ErrorViewInterface;

class Error extends AbstractWithViewData
{
    const UNAUTHORIZED   = 401;
    const ACCESS_DENIED  = 403;
    const FILE_NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     * @param null|ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, $response = null)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->data     = $this->retrieveViewDta($request);
        $this->data->setRawData($this->request->getAttributes());
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
     * @return ResponseInterface
     */
    public function asView($status)
    {
        /** @var ErrorViewInterface $view */
        if (!$view = RequestHelper::getService($this->request, ErrorViewInterface::class)) {
            throw new \BadMethodCallException;
        }
        $stream = $view->getStream($status, $this->data);
        return $this->respond($stream, $status);
    }

    /**
     * @return ResponseInterface
     */
    public function unauthorized()
    {
        return $this->asView(self::UNAUTHORIZED);
    }

    /**
     * @return ResponseInterface
     */
    public function forbidden()
    {
        return $this->asView(self::ACCESS_DENIED);
    }

    /**
     * @return ResponseInterface
     */
    public function notFound()
    {
        return $this->asView(self::FILE_NOT_FOUND);
    }
}
