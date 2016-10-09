<?php
namespace Tuum\Respond\Helper;

use Interop\Container\ContainerInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Interfaces\RenderErrorInterface;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\Tuum;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

class ServiceProvider
{
    /**
     * @var string
     */
    public static $option_name = ServiceOptions::class;

    /**
     * @param ContainerInterface $c
     * @param string             $name
     * @return null
     */
    public static function option(ContainerInterface $c, $name)
    {
        $options = $c->get(self::$option_name);
        if ($options instanceof ServiceOptions) {
            $options = $options->toArray();
        }
        return array_key_exists($name, $options) ? $options[$name] : null;
    }
    
    /**
     * @return callable[]
     */
    public function getServices()
    {
        return [
            Responder::class               => [self::class, 'getResponder'],
            View::class                    => [self::class, 'getView'],
            Redirect::class                => [self::class, 'getRedirect'],
            Error::class                   => [self::class, 'getError'],
            SessionStorageInterface::class => [self::class, 'getSessionStorage'],
            RenderErrorInterface::class    => [self::class, 'getErrorView'],
            RendererInterface::class       => [self::class, 'getRenderer'],
        ];
    }


    /**
     * @param ContainerInterface $c
     * @return Responder
     */
    public static function getResponder(ContainerInterface $c)
    {
        return new Responder($c);
    }

    /**
     * @param ContainerInterface $c
     * @return View
     */
    public static function getView(ContainerInterface $c)
    {
        $contentFile = self::option($c, 'content-file');

        return new View($c->get(RendererInterface::class), $contentFile, $c);
    }

    /**
     * @return Redirect
     */
    public static function getRedirect()
    {
        return new Redirect();
    }

    /**
     * @param ContainerInterface $c
     * @return SessionStorage
     */
    public static function getSessionStorage(ContainerInterface $c)
    {
        $appName = self::option($c, 'app-name');

        return SessionStorage::forge($appName);
    }

    /**
     * @param ContainerInterface $c
     * @return RenderErrorInterface
     */
    public static function getErrorView(ContainerInterface $c)
    {
        $errorFiles = self::option($c, 'error-files');

        return ErrorView::forge($c->get(RendererInterface::class), $errorFiles);
    }

    /**
     * @param ContainerInterface $c
     * @return Error
     */
    public static function getError(ContainerInterface $c)
    {
        return new Error($c->get(RenderErrorInterface::class));
    }

    /**
     * @param ContainerInterface $c
     * @return RendererInterface
     */
    public static function getRenderer(ContainerInterface $c)
    {
        $view   = self::option($c, 'renderer');
        $method = 'getRenderer' . ucwords($view);
        return self::$method($c);
    }

    /**
     * @param ContainerInterface $c
     * @return Plates
     */
    public static function getRendererPlates(ContainerInterface $c)
    {
        $templatePath  = self::option($c, 'template-path');
        $platesOptions = self::option($c, 'renderer-options');
        $callback      = $platesOptions['callback'];

        return Plates::forge($templatePath, $callback);
    }

    /**
     * @param ContainerInterface $c
     * @return Twig
     */
    public static function getRendererTwig(ContainerInterface $c)
    {
        $templatePath = self::option($c, 'template-path');
        $twigOptions  = self::option($c, 'renderer-options');
        $callback     = $twigOptions['callback'];
        $options      = $twigOptions['options'];

        return Twig::forge($templatePath, $options, $callback);
    }

    /**
     * @param ContainerInterface $c
     * @return Tuum
     */
    public static function getRendererTuum(ContainerInterface $c)
    {
        $templatePath  = self::option($c, 'template-path');
        $platesOptions = self::option($c, 'renderer-options');
        $callback      = $platesOptions['callback'];

        return Tuum::forge($templatePath, $callback);
    }
}