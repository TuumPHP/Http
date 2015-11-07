<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ViewerInterface;

class View extends AbstractWithViewData
{
    const OK = 200;

    /**
     * a view file to render a string content.
     *
     * @var null|string
     */
    public $content_view;

    /**
     * @var ViewerInterface
     */
    protected $view;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ViewerInterface $view
     * @param null|string     $content_view
     */
    public function __construct(ViewerInterface $view, $content_view = null)
    {
        $this->view         = $view;
        $this->content_view = $content_view;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a view response.
    // +----------------------------------------------------------------------+
    /**
     * creates a generic response.
     *
     * @param string $input
     * @param int                             $status
     * @param array                           $header
     * @return ResponseInterface
     */
    public function asResponse($input, $status = self::OK, array $header = [])
    {
        return ResponseHelper::fill($this->response, $input, $status, $header);
    }

    /**
     * @param string $file
     * @return ResponseInterface
     */
    private function asViewStream($file)
    {
        $this->data->setViewFile($file);
        return $this->view->withView($this->request, $this->response, $this->data);
    }

    /**
     * creates a Response with as template view file, $file.
     *
     * @param string $file
     * @return ResponseInterface
     */
    public function asView($file)
    {
        return $this->asViewStream($file);
    }

    /**
     * creates a Response of view with given $content as a contents.
     * use this to view a main contents with layout.
     *
     * @param string      $content
     * @param string|null $contents_file
     * @return ResponseInterface
     */
    public function asContents($content, $contents_file = null)
    {
        $contents_file = $contents_file ?: $this->content_view;
        $this->data->setData('contents', $content);

        return $this->asViewStream($contents_file);
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
     * @param string                          $filename
     * @param bool                            $attach download as attachment if true, or inline if false.
     * @param string|null                     $mime
     * @return ResponseInterface
     */
    public function asDownload($content, $filename, $attach = true, $mime = null)
    {
        if (is_resource($content)) {
            rewind($content);
            $content = stream_get_contents($content);
        }
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
}