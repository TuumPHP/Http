<?php
namespace Tuum\Respond;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Tuum\Respond\Interfaces\NamedRoutesInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\ErrorFile;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\RawPhp;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\SessionStorage;

class Builder
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var array
     */
    private $renderInfo = [];

    /**
     * @var string
     */
    private $name;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $content_view;

    /**
     * @var array
     */
    private $error_option = [];

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var Error
     */
    private $error;

    /**
     * @var View
     */
    private $view;

    /**
     * @var SessionStorage
     */
    private $session;

    /**
     * @var NamedRoutesInterface
     */
    private $namedRoutes;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * Builder constructor.
     *
     * @param string $name
     */
    public function __construct(string $name = 'App')
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public static function forge(string $name = 'App'): self
    {
        return new self($name);
    }

    /**
     * @param RendererInterface $renderer
     * @param string|null       $content_view
     * @return Builder
     */
    public function setRenderer(RendererInterface $renderer, string $content_view = null): self
    {
        $this->renderer = $renderer;
        $this->content_view = $content_view;
        return $this;
    }

    /**
     * @param string   $renderer
     * @param string   $root
     * @param array    $options
     * @param callable $callable
     * @return $this
     */
    public function setRendererInfo(string $renderer, string $root, array $options = [], callable $callable = null): self
    {
        $this->renderInfo[$renderer] = [
            'renderer' => $renderer,
            'root' => $root,
            'options' => $options,
            'callable' => $callable,
        ];
        return $this;
    }

    private function makeRenderer(): RendererInterface
    {
        foreach($this->renderInfo as $renderer => $info) {break;}
        if (!isset($renderer)) {
            throw new \InvalidArgumentException('renderer info is not set.');
        }
        $maker = 'makeRenderer' . ucwords($renderer);
        return $this->$maker($renderer);
    }

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @return Builder
     */
    public function setResponseFactory(ResponseFactoryInterface $responseFactory): Builder
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    /**
     * @param StreamFactoryInterface $streamFactory
     * @return Builder
     */
    public function setStreamFactory(StreamFactoryInterface $streamFactory): Builder
    {
        $this->streamFactory = $streamFactory;

        return $this;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function makeRendererTwig(string $renderer): RendererInterface
    {
        $info = $this->renderInfo[$renderer];
        return Twig::forge($info['root'], $info['options'], $info['callable']);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function makeRendererPlates(string $renderer): RendererInterface
    {
        $info = $this->renderInfo[$renderer];
        return Plates::forge($info['root'], $info['callable']);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function makeRendererRawPhp(string $renderer): RendererInterface
    {
        $info = $this->renderInfo[$renderer];
        return RawPhp::forge($info['root']);
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param array $option
     * @return $this
     */
    public function setErrorOption(array $option): self
    {
        $this->error_option = $option;
        return $this;
    }

    /**
     * @param NamedRoutesInterface $routes
     * @return $this
     */
    public function setNamedRoutes(NamedRoutesInterface $routes): self
    {
        $this->namedRoutes = $routes;
        return $this;
    }

    public function getRenderer(): RendererInterface
    {
        return $this->renderer ?: $this->makeRenderer();
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function getContentViewFile(): ?string
    {
        return $this->content_view;
    }

    public function getView(): View
    {
        return $this->view ?:
            $this->view = new View($this);
    }

    public function getRedirect(): Redirect
    {
        return $this->redirect ?:
            $this->redirect = new Redirect(
                $this->getNamedRoutes()
            );
    }

    public function getError(): Error
    {
        return $this->error ?:
            $this->error = new Error(
                ErrorFile::forge($this->error_option),
                $this->getView()
            );
    }

    public function getSessionStorage(): SessionStorage
    {
        return $this->session ?:
            $this->session = SessionStorage::forge($this->name, $_COOKIE);
    }

    /**
     * @param SessionStorage $session
     * @return Builder
     */
    public function setSessionStorage(SessionStorage $session): self
    {
        $this->session = $session;
        return $this;
    }

    public function getNamedRoutes(): ?NamedRoutesInterface
    {
        return $this->namedRoutes;
    }
    
    public function getResponseFactory(): ?ResponseFactoryInterface
    {
        return $this->responseFactory;
    }
    
    public function getStreamFactory(): ?StreamFactoryInterface
    {
        return $this->streamFactory;
    }
}