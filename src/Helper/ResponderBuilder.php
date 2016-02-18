<?php
namespace Tuum\Respond\Helper;

use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Presenter;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Interfaces\ErrorViewInterface;
use Tuum\Respond\Interfaces\ViewerInterface;

class ResponderBuilder
{
    /**
     * build responder object based on $view (ViewerInterface) and
     * $error( ErrorViewInterface) services.
     *
     * @param ViewerInterface    $view
     * @param ErrorViewInterface $error
     * @param null|string        $content_view
     * @param null|callable      $resolver
     * @return Responder
     */
    public static function withServices(
        ViewerInterface $view,
        ErrorViewInterface $error,
        $content_view = null,
        $resolver = null
    ) {
        $self = new Responder(
            new View($view, $content_view, $resolver),
            new Redirect(),
            new Error($error),
            new ViewData()
        );

        return $self;
    }

    /**
     * build responder object based on $view (ViewerInterface) object
     * and options for errors.
     *
     * @param ViewerInterface $view
     * @param array           $errorOption
     * @param string|null     $content_view
     * @param null|callable   $resolver
     * @return Responder
     */
    public static function withView(ViewerInterface $view, $errorOption = [], $content_view = null, $resolver = null)
    {
        $errors = ErrorView::forge($view, $errorOption);

        return self::withServices($view, $errors, $content_view, $resolver);
    }
}