<?php
namespace Tuum\Respond;

use Psr\Container\ContainerInterface;
use Tuum\Respond\Builder\Container;

class Factory
{
    private $setting = [];

    /**
     * @var Builder\Builder 
     */
    private $provider;

    /**
     * @var Container 
     */
    private $container;

    public function __construct(array $setting)
    {
        $this->setting = $setting;
        $this->provider  = new Builder\Builder($this->setting);
        $this->container = new Container();
        $this->container->addProvider($this->provider);
    }
    
    public static function new(array $setting = []): self 
    {
        $self = new self($setting);
        return $self;
    }

    public static function forge(array $setting = []): Responder
    {
        $self = new self($setting);
        return $self->build();
    }
    
    public function set(string $id, $concrete): self
    {
        $this->container->set($id, $concrete);
        return $this;
    }
    
    public function setContainer(ContainerInterface $container): self
    {
        $this->container->setContainer($container);
        return $this;
    }

    public function setFactory(string $id, callable $factory): self
    {
        $this->container->setFactory($id, $factory);
        return $this;
    }

    public function build(): Responder
    {
        $responder = new Responder($this->container);
        
        return $responder;
    }
}