<?php
namespace Tuum\Respond\Helper;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;

/**
 * Class ReqAttr
 *
 * takes care of attributes of $request.
 *
 * @package Tuum\Respond\Helper
 */
class ReqAttr
{
    const BASE_PATH = 'basePath';
    const PATH_INFO = 'pathInfo';
    const METHOD    = 'method';
    const REFERRER  = 'referrer';

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
        if ($referrer = Respond::session($request)->get(self::REFERRER)) {
            return $referrer;
        }
        $info = $request->getServerParams();

        return array_key_exists('HTTP_REFERER', $info) ? $info['HTTP_REFERER'] : '';
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
     * sets a method to override using $key in post or query data.
     *
     * @param ServerRequestInterface $request
     * @param string                 $key
     * @return ServerRequestInterface
     */
    public static function withMethod(ServerRequestInterface $request, $key = '_method')
    {
        $data = $request->getParsedBody() + $request->getQueryParams();
        if (isset($data[$key])) {
            return $request->withAttribute(self::METHOD, $data[$key]);
        }

        return $request;
    }

    /**
     * gets a overridden method or http method.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function getMethod(ServerRequestInterface $request)
    {
        return $request->getAttribute(self::METHOD, $request->getMethod());
    }
}