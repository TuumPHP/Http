<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Interfaces\ViewerInterface;
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
    /**
     * @var Twig_Environment
     */
    private $renderer;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @param Twig_Environment $renderer
     * @param null|ViewHelper  $view
     */
    public function __construct($renderer, $view = null)
    {
        $this->renderer   = $renderer;
        $this->viewHelper = $view;
        $this->renderer->addGlobal('view', $view);
    }

    /**
     * @param string   $root
     * @param array    $options
     * @param callable $callable
     * @return static
     */
    public static function forge($root, array $options = [], $callable = null)
    {
        $loader = new Twig_Loader_Filesystem($root);
        $twig   = new Twig_Environment($loader, $options);
        if (is_callable($callable)) {
            $twig = call_user_func($callable, $twig);
        }

        return new static($twig, ViewHelper::forge());
    }

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param string                  $viewFile
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewFile, $viewData)
    {
        $viewHelper = $this->viewHelper->start($request, $response);
        $viewFile   = (substr($viewFile, -4) === '.twig') ?: $viewFile . '.twig';
        $view_data  = $this->setDataView($viewHelper, $viewData);

        $response->getBody()->write($this->renderer->render($viewFile, $view_data));

        return $response;
    }

    /**
     * @param ViewHelper        $viewHelper
     * @param ViewDataInterface $viewData
     * @return array
     */
    private function setDataView($viewHelper, $viewData)
    {
        if ($viewData instanceof ViewDataInterface) {
            $viewHelper->setViewData($viewData);
            return array_merge($viewData->getData(), $viewData->getRawData());
        } elseif (is_array($viewData)) {
            return $viewData;
        }

        return [];
    }
}