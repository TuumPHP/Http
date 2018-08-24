<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Interfaces\NamedRoutesInterface;
use Tuum\Respond\Service\SessionStorage;

class Redirect extends AbstractResponder
{
    const REFERRER = '_referrer';

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var NamedRoutesInterface
     */
    private $routes;

    /**
     * @param SessionStorage       $session
     * @param NamedRoutesInterface $routes
     */
    public function __construct(SessionStorage $session, NamedRoutesInterface $routes = null)
    {
        parent::__construct($session);
        $this->routes = $routes;
    }
    
    /**
     * @param array|string $query
     * @return Redirect
     */
    public function addQuery($query)
    {
        if (is_array($query)) {
            $query = http_build_query($query, null, '&');
        }
        $this->query = $this->query ? $this->query . '&' . $query: $query;
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a response for redirection.
    // +----------------------------------------------------------------------+
    /**
     * redirects to $uri.
     * the $uri must be a full uri (like http://...), or a UriInterface object.
     *
     * @param UriInterface     $uri
     * @return ResponseInterface
     */
    public function toAbsoluteUri(UriInterface $uri)
    {
        $uri = $uri->withQuery($this->query);
        $uri = (string)$uri;
        $this->session->savePayload($this->responder->getPayload($this->request));

        return $this->responder->getResponse()
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
        $referrer = parse_url($this->getReferrer());
        $uri = $this->request->getUri();
        if (isset($referrer['path'])) {
            $uri = $uri->withPath($referrer['path']);
        }
        if (isset($referrer['query'])) {
            $this->addQuery($referrer['query']);
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
        if ($referrer = $this->session->get(self::REFERRER)) {
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
     * @return ResponseInterface
     */
    public function toRoute($routeName, $options= [])
    {
        if (!$this->routes) {
            throw new \BadMethodCallException('NamedRoute service is not set');
        }
        $path = $this->routes->route($routeName, $options);
        return $this->toPath($path);
    }
}