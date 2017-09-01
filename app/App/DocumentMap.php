<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Locator\FileMap;
use Tuum\Respond\Responder;

class DocumentMap
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
     * factory for this class.
     *
     * @param Dispatcher $app
     * @return self
     */
    public static function forge($app)
    {
        $docs_dir = dirname(dirname(__DIR__)) . '/docs';
        $mapper   = FileMap::forge($docs_dir);
        $self     = new self($mapper, $app->get(Responder::class));

        return $self;
    }
    
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $args)
    {
        $path = isset($args['pathInfo']) && $args['pathInfo'] ? $args['pathInfo'] : $this->index_file;
        $info = $this->mapper->render($path);
        if (!$info->found()) {
            return $this->responder->error($request, $response)->notFound();
        }
        if ($fp = $info->getResource()) {
            return $this->responder->view($request, $response)->asFileContents($fp, $info->getMimeType());
        }
        $view = $this->responder->view($request, $response);
        return $view->asContents($info->getContents(), 'layouts/contents-docs');
    }
}