<?php
namespace Tuum\Respond\Helper;

use Interop\Container\ContainerInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

class Provider
{
    private $options = [];

    public $default_content_file = 'layout/content';

    public $default_app_name = 'tuum-app';

    public $default_error_files = [
        'default' => 'errors/error',
        'status'  => [
            404 => 'errors/notFound',      // for not found.
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
    private function option($key)
    {
        return array_key_exists($key, $this->options)
            ? $this->options[$key]
            : null;
    }

    /**
     * @return array
     */
    public function getRespondList()
    {
        return [
            View::class           => 'getView',
            SessionStorage::class => 'getSessionStorage',
            ErrorView::class      => 'getErrorView',
            Error::class          => 'getError',
            Plates::class         => 'getRendererPlates',
            Twig::class           => 'getRendererTwig',
        ];
    }

    /**
     * @param ContainerInterface $c
     * @return View
     */
    public function getView(ContainerInterface $c)
    {
        $contentFile = $this->option('content-file') ?: $this->default_content_file;

        return new View($c->get(RendererInterface::class), $contentFile, $c);
    }

    /**
     * @return SessionStorage
     */
    public function getSessionStorage()
    {
        $appName = $this->option('app-name') ?: $this->default_app_name;

        return SessionStorage::forge($appName);
    }

    /**
     * @param ContainerInterface $c
     * @return ErrorView
     */
    public function getErrorView(ContainerInterface $c)
    {
        $errorFiles = $this->option('error-files') ?: $this->default_error_files;

        return ErrorView::forge($c->get(RendererInterface::class), $errorFiles);
    }

    /**
     * @param ContainerInterface $c
     * @return Error
     */
    public function getError(ContainerInterface $c)
    {
        return new Error($c->get(ErrorView::class));
    }

    /**
     * @return Plates
     */
    public function getRendererPlates()
    {
        $templatePath = $this->option('template-path');
        $setupPlates  = $this->option('plate-setup');

        return Plates::forge($templatePath, $setupPlates);
    }

    /**
     * @return Twig
     */
    public function getRendererTwig()
    {
        $templatePath = $this->option('template-path');
        $twigOptions  = $this->option('twig-options') ?: [];
        $setupTwig    = $this->option('twig-setup');

        return Twig::forge($templatePath, $twigOptions, $setupTwig);
    }
}