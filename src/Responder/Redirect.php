<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewData;

class Redirect extends AbstractWithViewData
{
    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param SessionStorageInterface $session
     */
    public function __construct(SessionStorageInterface $session)
    {
        $this->session  = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @param null|ResponseInterface $response
     * @return Redirect
     */
    public static function forge(ServerRequestInterface $request, $response = null)
    {
        $responder = new static(
            RequestHelper::getService($request, SessionStorageInterface::class)
        );
        return $responder->withRequest($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return Redirect
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        $self = $this->cloneWithRequest($request, $response);
        return $self;
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
    public function toBasePath($path = '', $query = '')
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