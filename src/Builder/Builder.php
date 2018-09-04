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

class Builder
{
    const ID = 'Tuum-Respond-Setup';

    /**
     * @return callable[]
     */
    public function getProviders()
    {
        return [
            self::ID    => [$this, 'setup'],
            View::class => [$this, 'getView'],
        ];
    }

    public function setup()
    {
        return [
            'content_view'  => 'layout/content_view',
            'error_options' => [
                'path'    => 'errors',
                'default' => 'error',
                'status'  => [
                    401 => 'unauthorized',  // for login error.
                    403 => 'forbidden',     // for CSRF token error.
                    404 => 'notFound',      // for not found.
                ],
                'files'   => [],
            ],
        ];
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

}
