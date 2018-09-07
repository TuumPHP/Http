<?php
namespace App\Demo\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tuum\Respond\Responder;

class NotFound implements MiddlewareInterface
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
        return $this->responder->error($request)->notFound();
    }
}