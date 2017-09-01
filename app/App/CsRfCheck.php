<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;

class CsRfCheck
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var callable
     */
    private $next;

    /**
     * CsRfCheck constructor.
     *
     * @param Responder $responder
     * @param callable  $next
     */
    public function __construct($responder, $next)
    {
        $this->responder = $responder;
        $this->next = $next;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $next = $this->next;
        $session = $this->responder->session();
        /** @var ServerRequestInterface $request */
        $request = $request->withAttribute('_token', $session->getToken());
        if ($request->getMethod() !== 'POST') {
            return $next($request, $response);
        }
        $post    = $request->getParsedBody();
        $token   = isset($post['_token']) ? $post['_token'] : '';
        if (!$session->validateToken($token)) {
            return $this->responder->error($request, $response)->forbidden();
        }
        return $next($request, $response);
    }

}