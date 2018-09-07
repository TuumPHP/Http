<?php
namespace App\Demo\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tuum\Respond\Responder;

class CsRfToken implements MiddlewareInterface
{
    /**
     * @var Responder
     */
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            if (!$this->validateToken($request)) {
                return $this->responder->error($request)->forbidden();
            }
        }

        return $handler->handle($request);
    }

    private function validateToken(ServerRequestInterface $request): bool
    {
        $post    = $request->getParsedBody();
        $token   = isset($post['_token']) ? $post['_token'] : '';
        $session = $this->responder->session();

        return $session->validateToken($token);
    }
}