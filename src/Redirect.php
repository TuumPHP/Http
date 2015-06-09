<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Http\Service\ViewData;
use Tuum\Http\Service\AbstractWithViewData;

class Redirect extends AbstractWithViewData
{
    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     * @param null|ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, $response = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->data = RequestHelper::getService($this->request, ViewData::class) ?: new ViewData();

        foreach ([ViewData::INPUTS, ViewData::ERRORS, ViewData::MESSAGE] as $key) {
            $value = $this->request->getAttribute($key);
            RequestHelper::setFlash($this->request, $key, $value);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param null|ResponseInterface $response
     * @return static
     */
    public static function forge(ServerRequestInterface $request, $response = null)
    {
        return new static($request, $response);
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface|string $uri
     * @return ResponseInterface
     */
    public function toAbsoluteUri($uri)
    {
        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }
        RequestHelper::setFlash($this->request, ViewData::MY_KEY, $this->data);
        return ResponseHelper::composeResponse($this->response, 'php://memory', 302, ['Location' => $uri]);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string $path
     * @return ResponseInterface
     */
    public function toPath($path)
    {
        $uri = $this->request->getUri()->withPath($path);
        return $this->toAbsoluteUri($uri);
    }

    /**
     * @param string $path
     * @param string $query
     * @return ResponseInterface
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
     * @return ResponseInterface
     */
    public function toReferrer()
    {
        $referrer = RequestHelper::getReferrer($this->request);
        return $this->toAbsoluteUri($referrer);
    }

}