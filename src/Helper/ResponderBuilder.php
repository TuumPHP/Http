<?php
namespace Tuum\Respond\Helper;

use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\RenderError;
use Tuum\Respond\Interfaces\RenderErrorInterface;

class ResponderBuilder
{
    /**
     * build responder object based on $view (ViewerInterface) and
     * $error( ErrorViewInterface) services.
     *
     * @param RendererInterface    $view
     * @param RenderErrorInterface $error
     * @param null|string          $content_view
     * @param null|callable        $resolver
     * @return Responder
     */
    public static function withServices(
        RendererInterface $view,
        RenderErrorInterface $error,
        $content_view = null,
        $resolver = null
    ) {
        $self = new Responder(
            new View($view, $content_view, $resolver),
            new Redirect(),
            new Error($error)
        );

        return $self;
    }

    /**
     * build responder object based on $view (ViewerInterface) object
     * and options for errors.
     *
     * @param RendererInterface $viewer
     * @param array           $errorOption
     * @param string|null     $content_view
     * @param null|callable   $resolver
     * @return Responder
     */
    public static function withView(RendererInterface $viewer, $errorOption = [], $content_view = null, $resolver = null)
    {
        $view  = new View($viewer, $content_view, $resolver);
        $error = RenderError::forge($viewer, $errorOption);

        $self = new Responder(
            $view,
            new Redirect(),
            new Error($error)
        );

        return $self;
    }
}