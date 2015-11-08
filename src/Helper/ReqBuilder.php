<?php
namespace Tuum\Respond\Helper;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Uri;

class ReqBuilder
{
    const APP_NAME = 'tuum-app';

    /**
     * creates a new $request based on $path and $method.
     *
     * @param string $path
     * @param string $method
     * @param array  $query
     * @param array  $post
     * @return ServerRequestInterface
     */
    public static function createFromPath(
        $path,
        $method = 'GET',
        $query = [],
        $post = []
    ) {
        $request = new ServerRequest(
            [],
            [],
            new Uri($path),
            $method ?: 'GET',
            'php://input',
            []
        );

        return $request
            ->withQueryParams($query)
            ->withParsedBody($post);
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
    private static function arrayGet($array, $key, $default = [])
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }
}
