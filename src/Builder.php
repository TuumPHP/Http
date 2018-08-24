<?php
namespace Tuum\Respond;

use Psr\Container\ContainerInterface;
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
     * Builder constructor.
     *
     * @param string $name
     */
    public function __construct($name = 'App')
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public static function forge($name = 'App')
    {
        return new self($name);
    }

    /**
     * @param RendererInterface $renderer
     * @param string|null       $content_view
     * @return $this
     */
    public function setRenderer(RendererInterface $renderer, $content_view = null)
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
    public function setRendererInfo($renderer, $root, array $options = [], $callable = null)
    {
        $this->renderInfo[$renderer] = [
            'renderer' => $renderer,
            'root' => $root,
            'options' => $options,
            'callable' => $callable,
        ];
        return $this;
    }

    /**
     * @return RendererInterface
     */
    private function makeRenderer()
    {
        foreach($this->renderInfo as $renderer => $info) {break;}
        if (!isset($renderer)) {
            throw new \InvalidArgumentException('renderer info is not set.');
        }
        $maker = 'makeRenderer' . ucwords($renderer);
        return $this->$maker($renderer);
    }
    
    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param string $renderer
     * @return RendererInterface
     */
    private function makeRendererTwig($renderer)
    {
        $info = $this->renderInfo[$renderer];
        return Twig::forge($info['root'], $info['options'], $info['callable']);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param string $renderer
     * @return RendererInterface
     */
    private function makeRendererPlates($renderer)
    {
        $info = $this->renderInfo[$renderer];
        return Plates::forge($info['root'], $info['callable']);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * @param string $renderer
     * @return RendererInterface
     */
    private function makeRendererRawPhp($renderer)
    {
        $info = $this->renderInfo[$renderer];
        return RawPhp::forge($info['root']);
    }

    /**
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @param array $option
     * @return $this
     */
    public function setErrorOption(array $option)
    {
        $this->error_option = $option;
        return $this;
    }

    /**
     * @param NamedRoutesInterface $routes
     * @return $this
     */
    public function setNamedRoutes(NamedRoutesInterface $routes)
    {
        $this->namedRoutes = $routes;
        return $this;
    }

    /**
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer ?: $this->makeRenderer();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getContentViewFile()
    {
        return $this->content_view;
    }

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view ?:
            $this->view = new View($this);
    }

    /**
     * @return Redirect
     */
    public function getRedirect()
    {
        return $this->redirect ?:
            $this->redirect = new Redirect(
                $this->getNamedRoutes()
            );
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error ?:
            $this->error = new Error(
                ErrorFile::forge($this->error_option),
                $this->getView()
            );
    }

    /**
     * @return SessionStorage
     */
    public function getSessionStorage()
    {
        return $this->session ?:
            $this->session = SessionStorage::forge($this->name, $_COOKIE);
    }

    public function setSessionStorage(SessionStorage $session): self
    {
        $this->session = $session;
        return $this;
    }

    public function getNamedRoutes()
    {
        return $this->namedRoutes;
    }
}