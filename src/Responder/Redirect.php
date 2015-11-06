<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\ViewData;

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
     * @param UriInterface|string $uri
     * @return ResponseInterface
     */
    public function toAbsoluteUri($uri)
    {
        if ($uri instanceof UriInterface) {
            $uri = (string)$uri;
        }
        if ($this->session) {
            $this->session->setFlash(ViewData::MY_KEY, $this->data);
        }

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $uri);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string $path
     * @param string $query
     * @return ResponseInterface
     */
    public function toPath($path, $query = '')
    {
        $uri = $this->request->getUri()->withPath($path);
        if (!is_null($query)) {
            $uri = $uri->withQuery($query);
        }

        return $this->toAbsoluteUri($uri);
    }

    /**
     * @param string $path
     * @param string $query
     * @return ResponseInterface
     */
    public function toBasePath($path = '', $query = '')
    {
        $path = '/' . ltrim($path, '/');
        $base = RequestHelper::getBasePath($this->request);
        $path = rtrim($base, '/') . $path;
        $path = rtrim($path, '/');
        $uri  = $this->request->getUri()->withPath($path);
        if (!is_null($query)) {
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