<?php
namespace Demo\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;

class RespondMiddleware
{
    const CSRF_TOKEN = '_token';
    
    /**
     * @var Responder
     */
    private $responder;

    /**
     * CsRfMiddleware constructor.
     *
     * @param Responder $responder
     */
    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // set CSRF token as request's attribute. 
        $session = $this->responder->session();
        $request = $request->withAttribute(self::CSRF_TOKEN, $session->getToken());

        if ($request->getMethod() !== 'POST') {
            return $next($request, $response);
        }
        // check token for post method. 
        $post    = $request->getParsedBody();
        $token   = isset($post[self::CSRF_TOKEN]) ? $post[self::CSRF_TOKEN] : '';
        if (!$session->validateToken($token)) {
            return $this->responder->error($request, $response)->forbidden();
        }
        return $next($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     * @return ResponseInterface
     */
    private function validateCsRfToken(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // set CSRF token as request's attribute. 
        $session = $this->responder->session();
        $request = $request->withAttribute(self::CSRF_TOKEN, $session->getToken());

        if ($request->getMethod() !== 'POST') {
            return $next($request, $response);
        }
        // check token for post method. 
        $post    = $request->getParsedBody();
        $token   = isset($post[self::CSRF_TOKEN]) ? $post[self::CSRF_TOKEN] : '';
        if (!$session->validateToken($token)) {
            return $this->responder->error($request, $response)->forbidden();
        }
        return $next($request, $response);
    }
}