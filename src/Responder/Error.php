<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ErrorViewInterface;
use Tuum\Respond\Service\SessionStorageInterface;

class Error extends AbstractWithViewData
{
    const UNAUTHORIZED   = 401;
    const ACCESS_DENIED  = 403;
    const FILE_NOT_FOUND = 404;
    const INTERNAL_ERROR = 500;

    /**
     * @var null|ErrorViewInterface
     */
    private $view;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param null|SessionStorageInterface $session
     * @param null|ErrorViewInterface $view
     */
    public function __construct($session = null, $view = null)
    {
        $this->session  = $session;
        $this->view     = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @return static
     */
    public static function forge(ServerRequestInterface $request)
    {
        $responder = new static(
            RequestHelper::getService($request, SessionStorageInterface::class),
            RequestHelper::getService($request, ErrorViewInterface::class)
        );
        return $responder->withRequest($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Error
     */
    public function withRequest(
        ServerRequestInterface $request
    ) {
        $self = $this->cloneWithRequest($request);
        $self->data->setRawData($request->getAttributes());
        return $self;
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
