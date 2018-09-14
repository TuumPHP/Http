<?php
namespace Tuum\Respond;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;

class Respond
{
    /**
     * @var Responder
     */
    private static $responder;

    /**
     * @return Responder
     */
    public static function getResponder(): ?Responder
    {
        return self::$responder;
    }
    
    public static function setResponder(Responder $responder)
    {
        self::$responder = $responder;
    }
    
    public static function setPayload(ServerRequestInterface $request, PayloadInterface $payload): ServerRequestInterface
    {
        return $request->withAttribute(PayloadInterface::class, $payload);
    }

    public static function getPayload(ServerRequestInterface $request): ?PayloadInterface
    {
        return $request->getAttribute(PayloadInterface::class);
    }
    
    public static function view(ServerRequestInterface $request): View
    {
        return self::getResponder()->view($request);
    }

    public static function redirect(ServerRequestInterface $request): Redirect
    {
        return self::getResponder()->redirect($request);
    }

    public static function error(ServerRequestInterface $request): Error
    {
        return self::getResponder()->error($request);
    }
    
    public static function getSession(ServerRequestInterface $request): ?SessionStorageInterface
    {
        return $request->getAttribute(SessionStorageInterface::class);
    }

    public static function setSession(ServerRequestInterface $request, SessionStorageInterface $session): ServerRequestInterface
    {
        return $request->withAttribute(SessionStorageInterface::class, $session);
    }
}