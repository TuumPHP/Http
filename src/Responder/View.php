<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewHelper;

class View extends AbstractResponder
{
    const OK = 200;

    /**
     * a view file to render a string content.
     *
     * @var null|string
     */
    public $content_view = 'layouts/contents';

    /**
     * @var Builder
     */
    private $builder;

    /**
     * Renderer constructor.
     *
     * @param Builder            $builder
     * @param SessionStorage     $session
     */
    public function __construct(Builder $builder, $session)
    {
        parent::__construct($session);
        $this->builder  = $builder;
        $this->content_view = $builder->getContentViewFile() ?: $this->content_view;
    }
    
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
     * @return ViewHelper
     */
    public function getViewHelper()
    {
        $payload = $this->session->getPayload();

        return ViewHelper::forge($this->request, $this->response, $payload, $this->builder);
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
        $content = $this->renderContents($file, $data);
        $stream  = $this->response->getBody();
        $stream->rewind();
        $stream->write($content);

        return $this->response;
    }

    /**
     * @param string $file
     * @param array  $data
     * @return string
     */
    public function renderContents($file, $data = [])
    {
        $helper   = $this->getViewHelper();
        
        return $this->builder->getRenderer()->render($file, $helper, $data);
    }

    // ------------------------------------------------------------------------
    // useful shortcut methods
    // ------------------------------------------------------------------------
    /**
     * creates a Response of view with given $content as a contents.
     * use this to view a main contents with layout.
     *
     * @param string $content
     * @param string|null   $content_view
     * @param array  $data
     * @return ResponseInterface
     */
    public function asContents($content, $content_view = null, $data = [])
    {
        $content_view = $content_view ?: $this->content_view;
        $data['contents'] = $content;
        return $this->render($content_view, $data);
    }

    /**
     * @param callable $callable
     * @param string   $content_view
     * @param array    $data
     * @return ResponseInterface
     */
    public function asObContents($callable, $content_view = null, $data = [])
    {
        ob_start();
        $callable();
        $content = ob_get_contents();
        ob_end_clean();
        
        return $this->asContents($content, $content_view, $data);
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
        if ($presenter instanceof PresenterInterface) {
            return $this->callPresenter([$presenter, '__invoke'], $data);
        }
        if (is_callable($presenter)) {
            return $this->callPresenter($presenter, $data);
        }
        if ($resolver = $this->builder->getContainer()) {
            return $this->callPresenter($resolver->get($presenter), $data);
        }
        throw new \BadMethodCallException('cannot resolve a presenter.');
    }

    /**
     * @param callable                $callable
     * @param array|mixed   $data
     * @return ResponseInterface
     */
    private function callPresenter($callable, $data)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('resolver is not a callable.');
        }

        return call_user_func($callable, $this->request, $this->response, $data);
    }
}