<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Http\Service\ViewData;
use Tuum\Http\Service\WithViewDataTrait;
use Zend\Diactoros\Response;

class Redirect
{
    use WithViewDataTrait;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     * @param array                  $options
     */
    public function __construct(ServerRequestInterface $request, $options = [])
    {
        $this->request = $request;
        $this->setViewData($options, RequestHelper::getApp($request));

        foreach ([ViewData::INPUTS, ViewData::ERRORS, ViewData::MESSAGE] as $key) {
            $value = $this->request->getAttribute($key);
            RequestHelper::setFlash($this->request, $key, $value);
        }
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
    //  methods for saving data into session's flash.
    // +----------------------------------------------------------------------+
    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function withFlashData($key, $value)
    {
        RequestHelper::setFlash($this->request, $key, $value);
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface|string $uri
     * @return ResponseInterface|Response
     */
    public function toAbsoluteUri($uri)
    {
        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }
        $data = $this->data->getData();
        RequestHelper::setFlash($this->request, $data);
        return ResponseHelper::createResponse('php://memory', 302, ['Location' => $uri]);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string $path
     * @return ResponseInterface|Response
     */
    public function toPath($path)
    {
        $uri = $this->request->getUri()->withPath($path);
        return $this->toAbsoluteUri($uri);
    }

    /**
     * @param string $path
     * @param string $query
     * @return ResponseInterface|Response
     */
    public function toBasePath($path = '', $query='')
    {
        $path = '/' . ltrim($path, '/');
        $base = RequestHelper::getBasePath($this->request);
        $path = $base . $path;
        $path = rtrim($path, '/');
        $uri  = $this->request->getUri()->withPath($path);
        if ($query) {
            $uri = $uri->withQuery($query);
        }
        return $this->toAbsoluteUri($uri);
    }

    /**
     * @return Response
     */
    public function toReferrer()
    {
        $referrer = RequestHelper::getReferrer($this->request);
        return $this->toAbsoluteUri($referrer);
    }

}