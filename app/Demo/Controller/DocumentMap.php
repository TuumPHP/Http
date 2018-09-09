<?php
namespace App\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Locator\FileMap;
use Tuum\Respond\Controller\AbstractRequestHandler;
use Tuum\Respond\Responder;

class DocumentMap extends AbstractRequestHandler
{
    /**
     * @var FileMap
     */
    private $mapper;

    /**
     * @var Responder
     */
    private $responder;

    /**
     * @var string
     */
    public $index_file = 'readme';

    public function __construct(FileMap $mapper, $responder)
    {
        $this->mapper    = $mapper;
        $this->responder = $responder;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke($request)
    {
        $args = $request->getQueryParams();
        $path = isset($args['pathInfo']) && $args['pathInfo'] ? $args['pathInfo'] : $this->index_file;
        $info = $this->mapper->render($path);
        if (!$info->found()) {
            return $this->responder->error($request)->notFound();
        }
        if ($fp = $info->getResource()) {
            return $this->responder->view($request)->asFileContents($fp, $info->getMimeType());
        }
        $view = $this->responder->view($request);
        return $view->asContents($info->getContents(), 'layouts/contents-docs');
    }
}