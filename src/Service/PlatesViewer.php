<?php
namespace Tuum\Respond\Service;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Interfaces\ViewerInterface;

class PlatesViewer implements ViewerInterface
{
    /**
     * @var Engine
     */
    private $renderer;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @param Engine   $renderer
     * @param ViewHelper $view
     */
    public function __construct($renderer, $view = null)
    {
        $this->renderer   = $renderer;
        $this->viewHelper = $view;
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
        $renderer = new Engine($root);
        if (is_callable($callable)) {
            $renderer = call_user_func($callable, $renderer);
        }

        return new static($renderer, ViewHelper::forge());
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
        $view_data  = $this->setDataView($viewHelper, $viewData);

        $response->getBody()->write($this->renderer->render($viewFile, $view_data));

        return $response;
    }

    /**
     * @param ViewHelper              $viewHelper
     * @param mixed|ViewDataInterface $viewData
     * @return array
     */
    private function setDataView($viewHelper, $viewData)
    {
        if ($viewData instanceof ViewDataInterface) {
            $this->renderer->addData(['view' => $viewHelper->setViewData($viewData)]);
            $view_data = $viewData->getRawData();
        } elseif (is_array($viewData)) {
            $view_data = $viewData;
        } else {
            $view_data = [
                'view' => $viewData
            ];
        }

        return $view_data;
    }

}