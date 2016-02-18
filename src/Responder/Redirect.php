<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Interfaces\ViewDataInterface;

class Redirect extends AbstractWithViewData
{
    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     *
     */
    public function __construct()
    {
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface|string     $uri
     * @param mixed|ViewDataInterface $data
     * @return ResponseInterface
     */
    public function toAbsoluteUri($uri, $data = null)
    {
        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }
        if ($this->session && $data) {
            $this->session->setFlash(ViewDataInterface::MY_KEY, $data);
        }

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $uri);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string                  $path
     * @param string                  $query
     * @param mixed|ViewDataInterface $data
     * @return ResponseInterface
     */
    public function toPath($path, $query = '', $data = null)
    {
        $uri = $this->request->getUri()->withPath($path);
        if (!is_null($query)) {
            $uri = $uri->withQuery($query);
        }

        return $this->toAbsoluteUri($uri, $data);
    }

    /**
     * @param string                  $path
     * @param string                  $query
     * @param mixed|ViewDataInterface $data
     * @return ResponseInterface
     */
    public function toBasePath($path = '', $query = '', $data = null)
    {
        $path = '/' . ltrim($path, '/');
        $base = ReqAttr::getBasePath($this->request);
        $path = rtrim($base, '/') . $path;
        $path = rtrim($path, '/');

        return $this->toPath($path, $query, $data);
    }

    /**
     * @param mixed|ViewDataInterface $data
     * @return ResponseInterface
     */
    public function toReferrer($data = null)
    {
        $referrer = ReqAttr::getReferrer($this->request);

        return $this->toAbsoluteUri($referrer, $data);
    }

}