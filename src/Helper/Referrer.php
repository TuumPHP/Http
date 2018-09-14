<?php
namespace Tuum\Respond\Helper;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;

/**
 * Class Referrer
 *
 * under development.
 *
 * @package Tuum\Respond\Helper
 */
class Referrer
{
    const REFERRER = 'referrer';

    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function load(ServerRequestInterface $request)
    {
        $referrer = Respond::getSession($request)->get(self::REFERRER);

        return $request->withAttribute(self::REFERRER, $referrer);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     */
    public function save(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (ResponseHelper::isInformational($response)) {
            self::saveReferrer($request);
        }
    }

    /**
     * saves the referrer uri to session.
     *
     * @param ServerRequestInterface $request
     */
    private function saveReferrer(ServerRequestInterface $request)
    {
        Respond::getSession($request)->set(self::REFERRER, $request->getUri()->__toString());
    }


}