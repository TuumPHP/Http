<?php
namespace Tuum\Respond;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Service\SessionStorageInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class RequestHelper
{
    const APP_NAME = 'tuum-app';
    const BASE_PATH = 'basePath';
    const PATH_INFO = 'pathInfo';
    const REFERRER = 'referrer';

    /**
     * creates a new $request based on $path and $method.
     *
     * @param string $path
     * @param string $method
     * @return ServerRequestInterface
     */
    public static function createFromPath(
        $path,
        $method = 'GET'
    ) {
        $request = new ServerRequest(
            [],
            [],
            new Uri($path),
            $method ?: 'GET',
            'php://input',
            []
        );

        return $request;
    }

    /**
     * creates a new $request based on $GLOBALS array.
     *
     * @param array $globals
     * @return ServerRequest
     */
    public static function createFromGlobal(array $globals = [])
    {
        $server  = self::arrayGet($globals, '_SERVER', $_SERVER);
        $files   = self::arrayGet($globals, '_FILES', $_FILES);
        $cookies = self::arrayGet($globals, '_COOKIE', $_COOKIE);
        $query   = self::arrayGet($globals, '_GET', $_GET);
        $body    = self::arrayGet($globals, '_POST', $_POST);
        $request = ServerRequestFactory::fromGlobals(
            $server,
            $query,
            $body,
            $cookies,
            $files
        );

        return $request;
    }

    /**
     * a helper method to get a $key from an $array; for internal use.
     *
     * @param array  $array
     * @param string $key
     * @param array  $default
     * @return array
     */
    protected static function arrayGet($array, $key, $default = [])
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Responder
     */
    public static function getResponder(ServerRequestInterface $request)
    {
        return $request->getAttribute(Responder::class);
    }

    /**
     * set a base-path and path-info for matching.
     *
     * @param ServerRequestInterface $request
     * @param string                 $basePath
     * @param string|null            $pathInfo
     * @return ServerRequestInterface
     */
    public static function withBasePath(ServerRequestInterface $request, $basePath, $pathInfo = null)
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $basePath) !== 0) {
            throw new \InvalidArgumentException;
        }
        $pathInfo = is_null($pathInfo) ? substr($path, strlen($basePath)) : $pathInfo;

        return $request
            ->withAttribute(self::BASE_PATH, $basePath)
            ->withAttribute(self::PATH_INFO, $pathInfo);
    }

    /**
     * get a base-path, or uri's path if not set.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getBasePath(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::BASE_PATH, '');
    }

    /**
     * get a path-info, or uri's path if not set.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getPathInfo(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::PATH_INFO, null) ?: $request->getUri()->getPath();
    }

    /**
     * set a session storage.
     *
     * @param ServerRequestInterface  $request
     * @param SessionStorageInterface $session
     * @return ServerRequestInterface
     */
    public static function withSessionMgr(ServerRequestInterface $request, $session)
    {
        return $request->withAttribute(SessionStorageInterface::class, $session);
    }

    /**
     * get a session storage.
     *
     * @param ServerRequestInterface $request
     * @return SessionStorageInterface|null
     */
    public static function getSessionMgr(ServerRequestInterface $request)
    {
        if ($responder = self::getResponder($request)) {
            return $responder->getStorage();
        }
        return $request->getAttribute(SessionStorageInterface::class);
    }

    /**
     * set a value into the current session.
     *
     * @param ServerRequestInterface $request
     * @param string|array           $key
     * @param null                   $value
     */
    public static function setSession(ServerRequestInterface $request, $key, $value = null)
    {
        $segment = self::getSessionMgr($request);
        self::magicSet($segment, $key, $value, 'set');
    }

    /**
     * @param SessionStorageInterface $segment
     * @param string|array            $key
     * @param mixed                   $value
     * @param string                  $method
     */
    private static function magicSet($segment, $key, $value, $method)
    {
        if (!$segment) {
            return;
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $segment->$method($k, $v);
            }

            return;
        }
        $segment->$method($key, $value);
    }

    /**
     * get a value from the current session.
     *
     * @param ServerRequestInterface $request
     * @param string                 $key
     * @param null|mixed             $alt
     * @return mixed
     */
    public static function getSession(ServerRequestInterface $request, $key, $alt = null)
    {
        $segment = self::getSessionMgr($request);

        return $segment->get($key, $alt);

    }

    /**
     * set a value in the session as a flash data.
     * the value can be retrieved by getFlash in the subsequent request.
     *
     * @param ServerRequestInterface $request
     * @param string|array           $key
     * @param null                   $value
     */
    public static function setFlash(ServerRequestInterface $request, $key, $value = null)
    {
        $segment = self::getSessionMgr($request);
        self::magicSet($segment, $key, $value, 'setFlash');
    }

    /**
     * get a value in the *current* flash data (just set by setFlash).
     *
     * @param ServerRequestInterface $request
     * @param string                 $key
     * @param null|mixed             $alt
     * @return mixed
     */
    public static function getCurrFlash(ServerRequestInterface $request, $key, $alt = null)
    {
        $segment = self::getSessionMgr($request);

        return $segment->getFlashNext($key, $alt);
    }

    /**
     * get a value in the flash data from the previous request.
     *
     * @param ServerRequestInterface $request
     * @param string                 $key
     * @param null|mixed             $alt
     * @return mixed
     */
    public static function getFlash(ServerRequestInterface $request, $key, $alt = null)
    {
        $segment = self::getSessionMgr($request);

        return $segment->getFlash($key, $alt);
    }

    /**
     * set the referrer uri.
     *
     * @param ServerRequestInterface $request
     * @param string                 $referrer
     * @return ServerRequestInterface
     */
    public static function withReferrer(ServerRequestInterface $request, $referrer)
    {
        return $request->withAttribute(self::REFERRER, $referrer);
    }

    /**
     * get the referrer uri set by withReferrer, or the HTTP_REFERER if not set.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getReferrer(ServerRequestInterface $request)
    {
        if ($referrer = $request->getAttribute(self::REFERRER)) {
            return $referrer;
        }
        $info = $request->getServerParams();

        return array_key_exists('HTTP_REFERER', $info) ? $info['HTTP_REFERER'] : '';
    }
}
