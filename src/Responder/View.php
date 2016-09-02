<?php
namespace Tuum\Respond\Responder;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;

class View extends AbstractWithViewData
{
    const OK = 200;

    /**
     * a view file to render a string content.
     *
     * @var null|string
     */
    public $content_view = 'layouts/contents';

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @var ContainerInterface|null
     */
    public $resolver;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param RendererInterface $view
     * @param null|string     $content_view
     * @param null|ContainerInterface   $resolver
     */
    public function __construct(RendererInterface $view, $content_view = null, $resolver = null)
    {
        $this->renderer     = $view;
        $this->content_view = $content_view ?: $this->content_view;
        $this->resolver     = $resolver;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a view response.
    // +----------------------------------------------------------------------+
    /**
     * creates a generic response.
     *
     * @param string|resource $input
     * @param int             $status
     * @param array           $header
     * @return ResponseInterface
     */
    public function asResponse($input, $status = self::OK, array $header = [])
    {
        return ResponseHelper::fill($this->response, $input, $status, $header);
    }

    /**
     * @param string $file
     * @param array  $data
     * @return ResponseInterface
     */
    private function renderWithViewer($file, $data = [])
    {
        $helper    = ['view' => $this->getViewHelper()];
        $content = $this->renderer->__invoke($file, $data, $helper);
        $stream  = $this->response->getBody();
        $stream->rewind();
        $stream->write($content);
        
        return $this->response;
    }

    /**
     * creates a Response with as template view file, $file.
     *
     * @param string $file
     * @param array  $data
     * @return ResponseInterface
     */
    public function render($file, $data = [])
    {
        return $this->renderWithViewer($file, $data);
    }

    /**
     * creates a Response of view with given $content as a contents.
     * use this to view a main contents with layout.
     *
     * @param string                  $content
     * @return ResponseInterface
     */
    public function asContents($content, $data = [])
    {
        $data['contents'] = $content;
        return $this->renderWithViewer($this->content_view, $data);
    }

    /**
     * returns a string as a html text.
     *
     * @param string $text
     * @return ResponseInterface
     */
    public function asHtml($text)
    {
        return $this->asResponse($text, self::OK, ['Content-Type' => 'text/html']);
    }

    /**
     * returns a string as a plain text.
     *
     * @param string $text
     * @return ResponseInterface
     */
    public function asText($text)
    {
        return $this->asResponse($text, self::OK, ['Content-Type' => 'text/plain']);
    }

    /**
     * returns as JSON from an array of $data.
     *
     * @param array $data
     * @return ResponseInterface
     */
    public function asJson(array $data)
    {
        return $this->asResponse(json_encode($data), self::OK, ['Content-Type' => 'application/json']);
    }

    /**
     * creates a response of file contents.
     * A file can be a string of the file's pathName, or a file resource.
     *
     * @param string|resource $file_loc
     * @param string          $mime
     * @return ResponseInterface
     */
    public function asFileContents($file_loc, $mime)
    {
        if (is_string($file_loc)) {
            $contents = file_get_contents($file_loc);
        } elseif (is_resource($file_loc)) {
            rewind($file_loc);
            $contents = stream_get_contents($file_loc);
        } else {
            throw new \InvalidArgumentException;
        }

        return $this->asResponse($contents, self::OK, ['Content-Type' => $mime]);
    }

    /**
     * creates a response for downloading a contents.
     * A contents can be, a text string, a resource, or a stream.
     *
     * @param string|resource $content
     * @param string          $filename
     * @param bool            $attach download as attachment if true, or inline if false.
     * @param string|null     $mime
     * @return ResponseInterface
     */
    public function asDownload($content, $filename, $attach = true, $mime = null)
    {
        $type = $attach ? 'attachment' : 'inline';
        $mime = $mime ?: 'application/octet-stream';

        return $this->asResponse(
            $content,
            self::OK, [
            'Content-Disposition' => "{$type}; filename=\"{$filename}\"",
            'Content-Length'      => (string)strlen($content),
            'Content-Type'        => $mime,
            'Cache-Control'       => 'public', // for IE8
            'Pragma'              => 'public', // for IE8
        ]);
    }

    /**
     * calls the presenter to create a view to respond.
     *
     * @param callable|PresenterInterface|string $presenter
     * @param array                              $data
     * @return ResponseInterface
     */
    public function call($presenter, array $data = [])
    {
        $viewData = clone($this->viewData);
        $viewData->setData($data);
        if ($presenter instanceof PresenterInterface) {
            return $this->callPresenter([$presenter, '__invoke'], $viewData);
        }
        if (is_callable($presenter)) {
            return $this->callPresenter($presenter, $viewData);
        }
        if ($this->resolver) {
            return $this->callPresenter($this->resolver->get($presenter), $viewData);
        }
        throw new \BadMethodCallException('cannot resolve a presenter.');
    }

    /**
     * @param callable                $callable
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    private function callPresenter($callable, $viewData)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('resolver is not a callable.');
        }

        return call_user_func($callable, $this->request, $this->response, $viewData);
    }
}