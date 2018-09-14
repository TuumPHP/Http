<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Interfaces\NamedRoutesInterface;

class Redirect extends AbstractResponder
{
    const REFERRER = '_referrer';

    /**
     * @var NamedRoutesInterface
     */
    private $routes;

    /**
     * @param NamedRoutesInterface $routes
     */
    public function __construct(NamedRoutesInterface $routes = null)
    {
        parent::__construct();
        $this->routes = $routes;
    }
    
    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface $uri
     * @param array        $query
     * @return ResponseInterface
     */
    public function toAbsoluteUri(UriInterface $uri, array $query = [])
    {
        if (!empty($query)) {
            $uri = $uri->withQuery(http_build_query($query, null, '&'));
        }
        $uri = (string)$uri;
        $this->savePayload($this->request);

        return $this->responder->makeResponse(302, '', ['Location' => $uri]);
    }

    /**
     * redirects to a path in string.
     * uses current hosts and scheme.
     *
     * @param string $path
     * @param array  $query
     * @return ResponseInterface
     */
    public function toPath($path, array $query = [])
    {
        $uri = $this->request->getUri()->withPath($path);

        return $this->toAbsoluteUri($uri, $query);
    }

    /**
     * @param string $path
     * @param array  $query
     * @return ResponseInterface
     */
    public function toBasePath($path = '', array $query = [])
    {
        $path = '/' . ltrim($path, '/');
        $base = ReqAttr::getBasePath($this->request);
        $path = rtrim($base, '/') . $path;
        $path = rtrim($path, '/');

        return $this->toPath($path, $query);
    }

    /**
     * @return ResponseInterface
     */
    public function toReferrer()
    {
        $referrer = parse_url($this->getReferrer());
        $uri = $this->request->getUri();
        if (isset($referrer['path'])) {
            $uri = $uri->withPath($referrer['path']);
        }
        if (isset($referrer['query'])) {
            $uri = $uri->withQuery($referrer['query']);
        }
        if (isset($referrer['fragment'])) {
            $uri = $uri->withFragment($referrer['fragment']);
        }
        return $this->toAbsoluteUri($uri);
    }

    /**
     * @return string
     */
    private function getReferrer()
    {
        $server = $this->request->getServerParams();
        if (array_key_exists('HTTP_REFERER', $server)) {
            return $server['HTTP_REFERER'];
        }
        if ($referrer = $this->responder->session($this->request)->get(self::REFERRER)) {
            return $referrer;
        }
        if ($referrer = ReqAttr::getReferrer($this->request)) {
            return $referrer;
        }
        throw new \BadMethodCallException('referrer not set');
    }

    /**
     * @param string $routeName
     * @param array  $options
     * @param array  $query
     * @return ResponseInterface
     */
    public function toRoute($routeName, $options= [], array $query = [])
    {
        if (!$this->routes) {
            throw new \BadMethodCallException('NamedRoute service is not set');
        }
        $path = $this->routes->route($routeName, $options);
        return $this->toPath($path, $query);
    }
}