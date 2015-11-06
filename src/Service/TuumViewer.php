<?php
namespace Tuum\Respond\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param Renderer $renderer
     */
    public function __construct($renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * creates a new ViewStream with Tuum\Renderer.
     * set $root for the root of the view/template directory.
     *
     * @param string $root
     * @return static
     */
    public static function forge($root)
    {
        $renderer = Renderer::forge($root);

        return new static($renderer);
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
        $view_data = $data->getRawData();
        $view_data['view'] = $view;
        return $view_data;
    }
}