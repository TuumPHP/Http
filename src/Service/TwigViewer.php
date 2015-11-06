<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class TwigStream
 *
 * uses Twig as template renderer as ViewStream.
 * include Twig 1.0 or later. Version 2.0 should work, as well.
 *
 * @package Tuum\Respond\Service
 */
class TwigViewer implements ViewerInterface
{
    use ViewerTrait;

    /**
     * @var Twig_Environment
     */
    private $renderer;

    /**
     * @param Twig_Environment $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param string $root
     * @param array  $options
     * @return static
     */
    public static function forge($root, array $options = [])
    {
        $loader = new Twig_Loader_Filesystem($root);
        $twig   = new Twig_Environment($loader, $options);
        return new static($twig);
    }

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        $view_file = $view->getViewFile();
        $view_file       = substr($view_file, -4) === '.twig' ?: $view_file . '.twig';
        $view_data = $this->setDataView($view);

        $response->getBody()->write($this->renderer->render($view_file, $view_data));
        return $response;
    }

    /**
     * @param ViewData $data
     * @return array
     */
    private function setDataView($data)
    {
        if (!$data) {
            return [];
        }
        $view = $this->forgeDataView($data);
        $this->renderer->addGlobal('viewData', $view);
        $view_data = $data->getData();
        return $view_data;
    }
}