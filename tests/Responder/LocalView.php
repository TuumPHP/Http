<?php
namespace tests\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Interfaces\ViewerInterface;

class LocalView implements ViewerInterface
{
    /**
     * @var string
     */
    public $view_file;

    /**
     * @var ViewData
     */
    public $data;

    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $viewFile
     * @param mixed|ViewData         $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewFile, $viewData)
    {
        if ($viewData instanceof ViewDataInterface) {
            $content = implode(',', $viewData->getData());
        } elseif (is_array($viewData)) {
            $content = implode(',', $viewData);
        } else {
            $content = '';
        }
        $this->view_file = $viewFile;
        $this->data      = $viewData;
        $response        = $response->withHeader('ViewFile', $viewFile);
        $response->getBody()->write($content);
        return $response;
    }
}