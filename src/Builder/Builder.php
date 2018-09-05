<?php
namespace Tuum\Respond\Builder;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
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

class Builder implements ContainerInterface
{
    const ID = 'Tuum-Respond-Setup';

    /**
     * @var array
     */
    private $setup = [];

    /**
     * @var callable[] 
     */
    private $providers = [];
    
    /**
     * Builder constructor.
     *
     * @param array $setup
     */
    public function __construct($setup = [])
    {
        $this->setup = $setup + [
                'content_view'    => 'layout/content_view',
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
            ];
        
        $this->providers = $this->getProviders();
    }

    /**
     * @return callable[]
     */
    public function getProviders()
    {
        return [
            self::ID                  => [$this, 'getSetup'],
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

    public function getSetup()
    {
        return $this->setup;
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

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }
        $factory = $this->providers[$id];
        if (is_callable($factory)) {
            return $factory($this);
        }
        throw new ContainerException(sprintf('Failed to build the id (%s).', $id));

    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->providers);
    }
}
