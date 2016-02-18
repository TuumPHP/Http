<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Form\DataView;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Interfaces\ViewerInterface;
use Tuum\View\Renderer;

/**
 * Class ViewStream
 *
 * uses Tuum/View as template renderer for ViewStream.
 * include 1.0 or later version.
 *
 * @package Tuum\Respond\Service
 */
class TuumViewer implements ViewerInterface
{
    use ViewerTrait;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var null|DataView
     */
    private $dataView;

    /**
     * @param Renderer $renderer
     * @param DataView $view
     */
    public function __construct($renderer, $view = null)
    {
        $this->renderer = $renderer;
        $this->dataView = $view;
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string   $root
     * @param callable $callable
     * @return static
     */
    public static function forge($root, $callable = null)
    {
        $renderer = Renderer::forge($root);
        if (is_callable($callable)) {
            $renderer = call_user_func($callable, $renderer);
        }

        return new static($renderer, new DataView());
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
        $view_data = $this->setDataView($viewData);

        $response->getBody()->write($this->renderer->render($viewFile, $view_data));

        return $response;
    }

    /**
     * @param mixed|ViewDataInterface $viewData
     * @return array
     */
    private function setDataView($viewData)
    {
        if ($viewData instanceof ViewDataInterface) {
            $view_data = $viewData->getRawData();
        } elseif (is_array($viewData)) {
            $view_data = $viewData;
        } else {
            $view_data = [];
        }
        $view_data['view'] = $this->forgeDataView($viewData, $this->dataView);

        return $view_data;
    }
}