<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Respond;

class Redirect extends AbstractWithViewData
{
    /**
     * @var string
     */
    private $query = '';

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @param array|string $query
     * @return Redirect
     */
    public function withQuery($query)
    {
        if (is_array($query)) {
            $query = http_build_query($query, null, '&');
        }
        $self = clone $this;
        $self->query = $self->query ? $self->query . '&' . $query: $query;
        return $self;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface|string     $uri
     * @return ResponseInterface
     */
    public function toAbsoluteUri($uri)
    {
        if ($uri instanceof UriInterface) {
            $uri = $uri->withQuery($this->query);
            $uri = (string)$uri;
        }
        Respond::session($this->request)->setFlash(ViewDataInterface::MY_KEY, $this->viewData);

        return $this->response
            ->withStatus(302)
            ->withHeader('Location', $uri);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string                  $path
     * @return ResponseInterface
     */
    public function toPath($path)
    {
        $uri = $this->request->getUri()->withPath($path);

        return $this->toAbsoluteUri($uri);
    }

    /**
     * @param string                  $path
     * @return ResponseInterface
     */
    public function toBasePath($path = '')
    {
        $path = '/' . ltrim($path, '/');
        $base = ReqAttr::getBasePath($this->request);
        $path = rtrim($base, '/') . $path;
        $path = rtrim($path, '/');

        return $this->toPath($path);
    }

    /**
     * @return ResponseInterface
     */
    public function toReferrer()
    {
        $referrer = ReqAttr::getReferrer($this->request);

        return $this->toAbsoluteUri($referrer);
    }

}