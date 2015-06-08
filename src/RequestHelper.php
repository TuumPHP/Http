<?php
namespace Tuum\Http;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Http\Service\SessionStorageInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RequestHelper
{
    const APP_NAME = 'tuum-app';
    const BASE_PATH = 'basePath';
    const PATH_INFO = 'pathInfo';
    const SESSION_MANAGER = 'sessionMgr';
    const REFERRER = 'referrer';

    /**
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
     * @param ServerRequestInterface $request
     * @param ContainerInterface            $app
     * @return ServerRequestInterface
     */
    public static function withApp(ServerRequestInterface $request, $app)
    {
        return $request->withAttribute(self::APP_NAME, $app);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ContainerInterface
     */
    public static function getApp(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::APP_NAME);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $key
     * @return mixed|null
     */
    public static function getContainer(ServerRequestInterface $request, $key)
    {
        $app = self::getApp($request);
        if ($app && $app->has($key)) {
            return $app->get($key);
        }
        return null;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $basePath
     * @return ServerRequestInterface
     */
    public static function withBasePath(ServerRequestInterface $request, $basePath)
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $basePath) !== 0) {
            throw new \InvalidArgumentException;
        }
        $pathInfo = substr($path, strlen($basePath));
        return $request
            ->withAttribute(self::BASE_PATH, $basePath)
            ->withAttribute(self::PATH_INFO, $pathInfo);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getBasePath(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::BASE_PATH, '');
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getPathInfo(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::PATH_INFO, null) ?: $request->getUri()->getPath();
    }

    /**
     * @param ServerRequestInterface  $request
     * @param SessionStorageInterface $session
     * @return ServerRequestInterface
     */
    public static function withSessionMgr(ServerRequestInterface $request, $session)
    {
        return $request->withAttribute(self::SESSION_MANAGER, $session);
    }

    /**
     * @param ServerRequestInterface $request
     * @return SessionStorageInterface
     */
    public static function getSessionMgr(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::SESSION_MANAGER) ?:
            self::getContainer($request, self::SESSION_MANAGER);
    }

    /**
     * @param ServerRequestInterface $request
     * @param string|array           $key
     * @param null                   $value
     */
    public static function setSession(ServerRequestInterface $request, $key, $value = null)
    {
        $segment = self::getSessionMgr($request);
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $segment->set($k, $v);
            }
            return;
        }
        $segment->set($key, $value);
    }

    /**
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
     * @param ServerRequestInterface $request
     * @param string|array           $key
     * @param null                   $value
     */
    public static function setFlash(ServerRequestInterface $request, $key, $value = null)
    {
        $segment = self::getSessionMgr($request);
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $segment->setFlash($k, $v);
            }
            return;
        }
        $segment->setFlash($key, $value);
    }

    /**
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
     * @param ServerRequestInterface $request
     * @param string                 $referrer
     * @return ServerRequestInterface
     */
    public static function withReferrer(ServerRequestInterface $request, $referrer)
    {
        return $request->withAttribute(self::REFERRER, $referrer);
    }

    /**
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
