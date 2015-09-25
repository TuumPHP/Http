<?php

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewStream;

/**
 * build $session and $responder, first.
 */

class RequestBuilder
{
    /**
     * @return ServerRequestInterface
     */
    public static function forge()
    {
        $session    = SessionStorage::forge('page-app');
        $viewStream = ViewStream::forge(__DIR__);
        $responder  = Responder::build(
            $viewStream,
            ErrorView::forge($viewStream, [])
        );

        /**
         * create a $request filled with various attributes.
         */
        $request = RequestHelper::createFromGlobal($GLOBALS);
        $request = RequestHelper::withResponder($request, $responder);
        $request = RequestHelper::withSessionMgr($request, $session);
        $request = RequestHelper::withMethod($request);

        return $request;
    }
}
