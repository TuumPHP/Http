<?php
namespace Tuum\Respond\Builder;

use Psr\Container\ContainerInterface;
use Tuum\Respond\Interfaces\ErrorFileInterface;
use Tuum\Respond\Interfaces\NamedRoutesInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorFile;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\RawPhp;
use Tuum\Respond\Service\Renderer\Twig;

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
        $this->setting = $setting + [
                'error_options'   => [
                    'path'    => 'errors',
                    'default' => 'error',
                    'status'  => [
                        401 => 'unauthorized',  // for login error.
                        403 => 'forbidden',     // for CSRF token error.
                        404 => 'notFound',      // for not found.
                    ],
                    'files'   => [],
                ],
                'template_dir'    => '/templates',
                'renderer_type'   => Twig::class,
                'twig-options'    => [],
                'twig-callable'   => null,
                'plates-callable' => null,
                'content_view'    => 'layout/content_view',
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
        ];
    }

    public function getSetting(): array
    {
        return $this->setting;
    }

    public function getView(ContainerInterface $container): View
    {
        $contentView = $container->get(self::ID)['content_view'] ?? '';
        return new View(
            $container->get(RendererInterface::class),
            $container->get($contentView)
        );
    }

    public function getRedirect(ContainerInterface $container): Redirect
    {
        return new Redirect(
            $container->get(NamedRoutesInterface::class)
        );
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
        $info = $container->get(self::ID);
        return Twig::forge($info['template_dir'], $info['twig-options'], $info['twig-callable']);
    }

    public function getRendererPlates(ContainerInterface $container): Plates
    {
        $info = $container->get(self::ID);
        return Plates::forge($info['template_dir'], $info['plates-callable']);
    }

    public function getRendererRawPhp(ContainerInterface $container): RawPhp
    {
        $info = $container->get(self::ID);
        return RawPhp::forge($info['template_dir']);
    }

    public function getRenderer(ContainerInterface $container): RendererInterface
    {
        $info = $container->get(self::ID);
        $type = $info['renderer_type'];
        return $container->get($type);
    }
}
