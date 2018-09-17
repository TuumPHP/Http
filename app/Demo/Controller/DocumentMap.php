<?php
namespace App\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
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
     * @param string $pathInfo
     * @return ResponseInterface
     */
    public function onGet($pathInfo)
    {
        $info = $this->mapper->render($pathInfo);
        if (!$info->found()) {
            return $this->error()->notFound();
        }
        if ($fp = $info->getResource()) {
            return $this->view()->asFileContents($fp, $info->getMimeType());
        }
        return $this->view()
                    ->asContents($info->getContents(), 'layouts/contents-docs');
    }
}