<?php
namespace Tuum\Respond\Builder;

use Psr\Container\ContainerInterface;
use Tuum\Respond\Interfaces\ErrorFileInterface;
use Tuum\Respond\Interfaces\NamedRoutesInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorFile;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\RawPhp;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

class Builder implements ServiceProviderInterface
{
    const ID = 'Tuum-Respond-Setup';

    /**
     * @var array
     */
    private $setting = [];

    /**
     * Builder constructor.
     *
     * @param array $setting
     */
    public function __construct($setting = [])
    {
        $viewOption = ($setting['view_options'] ?? []) + [
                'template_dir'    => '/templates',
                'renderer_type'   => Twig::class,
                'twig-options'    => [],
                'twig-callable'   => null,
                'plates-callable' => null,
                'content_view'    => 'layouts/content_view',
            ];
        $errorOption = ($setting['error_options'] ?? []) + [
                'path'    => 'errors',
                'default' => 'error',
                'status'  => [
                    401 => 'unauthorized',  // for login error.
                    403 => 'forbidden',     // for CSRF token error.
                    404 => 'notFound',      // for not found.
                ],
                'files'   => [],
            ];
        $this->setting = [
                'name'          => 'app',
                'error_options' => $errorOption,
                'view_options'  => $viewOption,
            ];
    }

    /**
     * @return callable[]
     */
    public function getFactories()
    {
        return [
            self::ID                  => [$this, 'getSetting'],
            View::class               => [$this, 'getView'],
            Redirect::class           => [$this, 'getRedirect'],
            ErrorFileInterface::class => [$this, 'getErrorFile'],
            Error::class              => [$this, 'getError'],
            Twig::class               => [$this, 'getRendererTwig'],
            Plates::class             => [$this, 'getRendererPlates'],
            RawPhp::class             => [$this, 'getRendererRawPhp'],
            RendererInterface::class  => [$this, 'getRenderer'],
            SessionStorageInterface::class => [$this, 'getSessionStorage'],
        ];
    }

    public function getSetting(): array
    {
        return $this->setting;
    }

    public function getView(ContainerInterface $container): View
    {
        $contentView = $container->get(self::ID)['view_options']['content_view'] ?? '';
        return new View(
            $container->get(RendererInterface::class),
            $contentView
        );
    }

    public function getRedirect(ContainerInterface $container): Redirect
    {
        $route = $container->has(NamedRoutesInterface::class) 
            ? $container->get(NamedRoutesInterface::class)
            : null;
        return new Redirect($route);
    }

    public function getErrorFile(ContainerInterface $container): ErrorFileInterface
    {
        $options = $container->get(self::ID)['error_options'] ?? [];
        return ErrorFile::forge(
            $options
        );
    }

    public function getError(ContainerInterface $container): Error
    {
        return new Error(
            $container->get(ErrorFileInterface::class)
        );
    }

    public function getRendererTwig(ContainerInterface $container): Twig
    {
        $info = $container->get(self::ID)['view_options'];
        return Twig::forge($info['template_dir'], $info['twig-options'], $info['twig-callable']);
    }

    public function getRendererPlates(ContainerInterface $container): Plates
    {
        $info = $container->get(self::ID)['view_options'];
        return Plates::forge($info['template_dir'], $info['plates-callable']);
    }

    public function getRendererRawPhp(ContainerInterface $container): RawPhp
    {
        $info = $container->get(self::ID)['view_options'];
        return RawPhp::forge($info['template_dir']);
    }

    public function getRenderer(ContainerInterface $container): RendererInterface
    {
        $info = $container->get(self::ID)['view_options'];
        $type = $info['renderer_type'];
        return $container->get($type);
    }

    public function getSessionStorage(ContainerInterface $container): SessionStorageInterface
    {
        $name = $container->get(self::ID)['name'] ?? 'app';
        return SessionStorage::forge($name, $_COOKIE);
    }

}
