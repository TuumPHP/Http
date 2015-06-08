<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Tuum\Http\Service\Value;
use Zend\Diactoros\Response;

class Redirect
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        foreach ([Value::INPUTS, Value::ERRORS, Value::MESSAGE] as $key) {
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
    public function with($key, $value)
    {
        RequestHelper::setFlash($this->request, $key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    private function withPush($key, $value)
    {
        $data = RequestHelper::getCurrFlash($this->request, $key, []);
        $data[] = $value;
        RequestHelper::setFlash($this->request, $key, $data);
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInputData(array $input)
    {
        return $this->with(Value::INPUTS, $input);
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withInputErrors(array $errors)
    {
        return $this->with(Value::ERRORS, $errors);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->withPush(Value::MESSAGE, Value::success($message));
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withAlertMsg($message)
    {
        $this->withPush(Value::MESSAGE, Value::alert($message));
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withErrorMsg($message)
    {
        $this->withPush(Value::MESSAGE, Value::error($message));
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