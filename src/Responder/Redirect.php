<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Interfaces\ViewDataInterface;

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
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function toAbsoluteUri($uri, $viewData = null)
    {
        if ($uri instanceof UriInterface) {
            $uri = $uri->withQuery($this->query);
            $uri = (string)$uri;
        }
        if ($this->session && $viewData) {
            $this->session->setFlash(ViewDataInterface::MY_KEY, $viewData);
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
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function toPath($path, $viewData = null)
    {
        $uri = $this->request->getUri()->withPath($path);

        return $this->toAbsoluteUri($uri, $viewData);
    }

    /**
     * @param string                  $path
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function toBasePath($path = '', $viewData = null)
    {
        $path = '/' . ltrim($path, '/');
        $base = ReqAttr::getBasePath($this->request);
        $path = rtrim($base, '/') . $path;
        $path = rtrim($path, '/');

        return $this->toPath($path, $viewData);
    }

    /**
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function toReferrer($viewData = null)
    {
        $referrer = ReqAttr::getReferrer($this->request);

        return $this->toAbsoluteUri($referrer, $viewData);
    }

}