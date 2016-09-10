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

class TuumProvider
{
    const TUUM_CONFIG = 'Tuum-configuration';

    /**
     * @var array
     */
    public $options
        = [
            'app-name'       => 'tuum-app',
            // rendering options
            'template-path'  => null,
            'content-file'   => 'layouts/contents',
            // set up renderer
            'renderer'       => 'plates',   // set renderer type: plates, twig, or tuum.
            'plates-options' => [
                'options'  => [],
                'callback' => null,
            ],
            'twig-options'   => [
                'options'  => [],
                'callback' => null,
            ],
            'tuum-options'   => [
                'options'  => [],
                'callback' => null,
            ],
            // set up error files
            'error-files'    => [
                'default' => 'errors/error',
                'status'  => [
                    404 => 'errors/notFound',      // for not found.
                ],
            ],
        ];

    /**
     * Provider constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Provider constructor.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * @return array
     */
    public function getServices()
    {
        $self = TuumProvider::class;
        return [
            self::TUUM_CONFIG              => function() {
                return $this->options;
            },
            Responder::class               => [$self, 'getResponder'],
            View::class                    => [$self, 'getView'],
            Redirect::class                => [$self, 'getRedirect'],
            Error::class                   => [$self, 'getError'],
            SessionStorageInterface::class => [$self, 'getSessionStorage'],
            RenderErrorInterface::class    => [$self, 'getErrorView'],
            RendererInterface::class       => [$self, 'getRenderer'],
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
        $contentFile = $c->get(self::TUUM_CONFIG)['content-file'];

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
        $appName = $c->get(self::TUUM_CONFIG)['app-name'];

        return SessionStorage::forge($appName);
    }

    /**
     * @param ContainerInterface $c
     * @return RenderErrorInterface
     */
    public static function getErrorView(ContainerInterface $c)
    {
        $errorFiles = $c->get(self::TUUM_CONFIG)['error-files'];

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
        $view   = $c->get(self::TUUM_CONFIG)['renderer'];
        $method = 'getRenderer' . ucwords($view);
        return self::$method($c);
    }

    /**
     * @param ContainerInterface $c
     * @return Plates
     */
    public static function getRendererPlates(ContainerInterface $c)
    {
        $templatePath  = $c->get(self::TUUM_CONFIG)['template-path'];
        $platesOptions = $c->get(self::TUUM_CONFIG)['plates-options'];
        $callback      = $platesOptions['callback'];

        return Plates::forge($templatePath, $callback);
    }

    /**
     * @param ContainerInterface $c
     * @return Twig
     */
    public static function getRendererTwig(ContainerInterface $c)
    {
        $templatePath = $c->get(self::TUUM_CONFIG)['template-path'];
        $twigOptions  = $c->get(self::TUUM_CONFIG)['twig-options'];
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
        $templatePath  = $c->get(self::TUUM_CONFIG)['template-path'];
        $platesOptions = $c->get(self::TUUM_CONFIG)['tuum-options'];
        $callback      = $platesOptions['callback'];

        return Tuum::forge($templatePath, $callback);
    }
}