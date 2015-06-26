<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ViewStreamInterface;
use Zend\Diactoros\Stream;

class View extends AbstractWithViewData
{
    const OK = 200;

    /**
     * @var ViewStreamInterface
     */
    protected $view;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ViewStreamInterface $view
     */
    public function __construct(ViewStreamInterface $view)
    {
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return View
     */
    public function withRequest(
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ) {
        $self = $this->cloneWithRequest($request, $response);
        $self->data->setRawData($request->getAttributes());
        return $self;
    }

    // +----------------------------------------------------------------------+
    //  methods for creating a view response.
    // +----------------------------------------------------------------------+
    /**
     * creates a generic response.
     *
     * @param string|StreamInterface|resource $input
     * @param int                             $status
     * @param array                           $header
     * @return ResponseInterface
     */
    public function asResponse($input, $status = self::OK, array $header = [])
    {
        return ResponseHelper::composeResponse($this->response, $input, $status, $header);
    }

    /**
     * @param string $method
     * @param string $data
     * @return ResponseInterface
     */
    private function asViewStream($method, $data)
    {
        $view = $this->view->$method($data, $this->data);
        return $this->asResponse($view);
    }

    /**
     * creates a Response with as template view file, $file.
     *
     * @param string $file
     * @return ResponseInterface
     */
    public function asView($file)
    {
        return $this->asViewStream('withView', $file);
    }

    /**
     * creates a Response of view with given $content as a contents.
     * use this to view a main contents with layout.
     *
     * @param string $content
     * @return ResponseInterface
     */
    public function asContents($content)
    {
        return $this->asViewStream('withContent', $content);
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
            $stream = new Stream(fopen($file_loc, 'rb'));
        } elseif (is_resource($file_loc)) {
            $stream = new Stream($file_loc);
        } else {
            throw new \InvalidArgumentException;
        }
        return $this->asResponse($stream, self::OK, ['Content-Type' => $mime]);
    }

    /**
     * creates a response for downloading a contents.
     * A contents can be, a text string, a resource, or a stream.
     *
     * @param string|StreamInterface|resource $content
     * @param string                          $filename
     * @param bool                            $attach download as attachment if true, or inline if false.
     * @param string|null                     $mime
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
}