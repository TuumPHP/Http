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
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

trait ProviderTrait
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    public $default_options
        = [
            'app-name'       => 'tuum-app',
            // rendering options
            'template-path'  => null,
            'content-file'   => 'layout/content',
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
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getDefaultOptions($key)
    {
        return array_key_exists($key, $this->default_options)
            ? $this->default_options[$key]
            : null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function option($key)
    {
        return array_key_exists($key, $this->options)
            ? $this->options[$key]
            : $this->getDefaultOptions($key);
    }

    /**
     * @return array
     */
    public function getRespondList()
    {
        return [
            Responder::class               => 'getResponder',
            View::class                    => 'getView',
            Redirect::class                => 'getRedirect',
            Error::class                   => 'getError',
            SessionStorageInterface::class => 'getSessionStorage',
            RenderErrorInterface::class    => 'getErrorView',
            RendererInterface::class       => 'getRenderer',
            Plates::class                  => 'getRendererPlates',
            Twig::class                    => 'getRendererTwig',
        ];
    }

    /**
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        static $singletons = [];
        if (!array_key_exists($name, $singletons)) {
            if (!method_exists($this, $name)) {
                throw new \BadMethodCallException("provider method '{$name}' not found. ");
            }
            $singletons[$name] = $this->$name($args[0]);
        }
        return $singletons[$name];
    }

    /**
     * @param ContainerInterface $c
     * @return Responder
     */
    protected function getResponder(ContainerInterface $c)
    {
        return new Responder($c);
    }

    /**
     * @param ContainerInterface $c
     * @return View
     */
    protected function getView(ContainerInterface $c)
    {
        $contentFile = $this->option('content-file');

        return new View($c->get(RendererInterface::class), $contentFile, $c);
    }

    /**
     * @return Redirect
     */
    protected function getRedirect()
    {
        return new Redirect();
    }

    /**
     * @return SessionStorage
     */
    protected function getSessionStorage()
    {
        $appName = $this->option('app-name');

        return SessionStorage::forge($appName);
    }

    /**
     * @param ContainerInterface $c
     * @return RenderErrorInterface
     */
    protected function getErrorView(ContainerInterface $c)
    {
        $errorFiles = $this->option('error-files');

        return ErrorView::forge($c->get(RendererInterface::class), $errorFiles);
    }

    /**
     * @param ContainerInterface $c
     * @return Error
     */
    protected function getError(ContainerInterface $c)
    {
        return new Error($c->get(RenderErrorInterface::class));
    }

    /**
     * @return RendererInterface
     */
    protected function getRenderer()
    {
        $view   = $this->option('renderer');
        $method = 'getRenderer' . ucwords($view);
        return $this->$method();
    }

    /**
     * @return Plates
     */
    protected function getRendererPlates()
    {
        $templatePath  = $this->option('template-path');
        $platesOptions = $this->option('plate-options');
        $callback      = $platesOptions['callback'];

        return Plates::forge($templatePath, $callback);
    }

    /**
     * @return Twig
     */
    protected function getRendererTwig()
    {
        $templatePath = $this->option('template-path');
        $twigOptions  = $this->option('twig-options');
        $callback     = $twigOptions['callback'];
        $options      = $twigOptions('options');

        return Twig::forge($templatePath, $options, $callback);
    }
}