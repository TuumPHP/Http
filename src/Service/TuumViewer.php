<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Form\DataView;
use Tuum\Respond\Responder\ViewData;
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
     * @param string    $root
     * @param callable  $callable
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        $view_file = $view->getViewFile();
        $view_data = $this->setDataView($view);

        $response->getBody()->write($this->renderer->render($view_file, $view_data));
        if ($status = $view->getStatus()) {
            $response = $response->withStatus($status);
        }

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
        $view              = $this->forgeDataView($data, $this->dataView);
        $view_data         = $data->getRawData();
        $view_data['view'] = $view;

        return $view_data;
    }
}