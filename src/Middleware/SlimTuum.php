<?php
namespace Tuum\Respond\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewStream;

class SlimTuum
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
    public static function forge($viewDir, $error_options, $cookie = null)
    {
        // check options.
        $cookie  = is_null($cookie) ?: $_COOKIE;
        $error_options += [
            'default' => 'errors/error',
            'status'  => [],
            'handler' => false,
        ];
        // construct responders and its dependent objects.
        $session = SessionStorage::forge('slim-tuum', $cookie);
        $stream  = ViewStream::forge($viewDir);
        $errors  = ErrorView::forge($stream, $error_options);
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