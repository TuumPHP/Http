<?php
namespace Tuum\Respond\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TwigStream;
use Tuum\Respond\Service\ViewStream;
use Tuum\Respond\Service\ViewStreamInterface;

/**
 * Class CommonSignature
 *
 * a sample middleware code for common signature: ($request, $response, $next),
 * that is used by Slim3, zend-stratigility, and Relay/Radar, for instance.
 * 
 * @package Tuum\Respond\Middleware
 * 
 */
class CommonSignature
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @param Responder $responder
     */
    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param string     $viewDir
     * @param array      $error_options
     * @param null|array $cookie
     * @return static
     */
    public static function forge($viewDir, array $error_options, $cookie = null)
    {
        $stream = ViewStream::forge($viewDir);

        return self::build($stream, $error_options, $cookie);
    }

    /**
     * @param string     $twigRoot
     * @param array      $twigOptions
     * @param array      $errorOptions
     * @param array|null $cookie
     * @return static
     */
    public static function forgeTwig($twigRoot, array $twigOptions, array $errorOptions, $cookie = null)
    {
        $stream = TwigStream::forge($twigRoot, $twigOptions);

        return self::build($stream, $errorOptions, $cookie);
    }

    /**
     * @param ViewStreamInterface $stream
     * @param array               $errors
     * @param array|null          $cookie
     * @return static
     */
    private static function build($stream, $errors, $cookie)
    {
        // check options.
        $cookie = is_null($cookie) ?: $_COOKIE;
        $errors += [
            'default' => 'errors/error',
            'status'  => [],
            'handler' => false,
        ];
        // construct responders and its dependent objects.
        $session = SessionStorage::forge('slim-tuum', $cookie);
        $errors  = ErrorView::forge($stream, $errors);
        $respond = Responder::build($stream, $errors)->withSession($session);
        $self    = new static($respond);

        return $self;

    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Closure                $next
     * @return mixed
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Closure $next
    ) {
        $request = $request->withAttribute(Responder::class, $this->responder);

        return $next($request, $response);
    }
}