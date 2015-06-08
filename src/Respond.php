<?php
namespace Tuum\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Http\Service\ViewStreamInterface;
use Tuum\Http\Service\ViewData;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class Respond
{
    const OK = 200;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $data = [];

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->data    = $this->request->getAttributes();
        if (!RequestHelper::getSessionMgr($request)) return;

        foreach ([ViewData::INPUTS, ViewData::ERRORS, ViewData::MESSAGE] as $key) {
            $value = RequestHelper::getFlash($request, $key);
            $this->data[$key] = $value;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return static
     */
    public static function forge(ServerRequestInterface $request)
    {
        return new static($request);
    }

    // +----------------------------------------------------------------------+
    //  methods for saving data for response.
    // +----------------------------------------------------------------------+
    /**
     * @param string|array $key
     * @param mixed        $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        }
        if (is_string($key)) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    protected function merge($key, $value)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = [];
        }
        $this->data[$key][] = $value;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInput(array $input)
    {
        return $this->with(ViewData::INPUTS, $input);
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withInputErrors(array $errors)
    {
        return $this->with(ViewData::ERRORS, $errors);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->merge(ViewData::MESSAGE, ViewData::success($message));
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withAlert($message)
    {
        $this->merge(ViewData::MESSAGE, ViewData::alert($message));
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withError($message)
    {
        $this->merge(ViewData::MESSAGE, ViewData::error($message));
        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function withFlashData($key, $value)
    {
        RequestHelper::setFlash($this->request, $key, $value);
        return $this;
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
        return ResponseHelper::createResponse($input, $status, $header);
    }

    /**
     * creates a Response with as template view file, $file.
     *
     * @param string $file
     * @return ResponseInterface
     */
    public function asView($file)
    {
        /** @var ViewStreamInterface $view */
        if (!$view = RequestHelper::getApp($this->request)->get(ViewStreamInterface::class)) {
            throw new \BadMethodCallException;
        }
        $view = $view->withView($file, $this->data);
        return $this->asResponse($view);
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
        /** @var ViewStreamInterface $view */
        if (!$view = RequestHelper::getApp($this->request)->get(ViewStreamInterface::class)) {
            throw new \BadMethodCallException;
        }
        $view = $view->withContent($content);
        return $this->asResponse($view);
    }

    /**
     * returns a string as a html text.
     *
     * @param string $text
     * @return Response
     */
    public function asHtml($text)
    {
        return new Response($text, self::OK, ['Content-Type' => 'text/html']);
    }

    /**
     * returns a string as a plain text.
     *
     * @param string $text
     * @return Response
     */
    public function asText($text)
    {
        return new Response($text, self::OK, ['Content-Type' => 'text/plain']);
    }

    /**
     * returns as JSON from an array of $data.
     *
     * @param array $data
     * @return Response
     */
    public function asJson(array $data)
    {
        return new Response(json_encode($data), self::OK, ['Content-Type' => 'application/json']);
    }

    /**
     * creates a response of file contents.
     * A file can be a string of the file's pathName, or a file resource.
     *
     * @param string|resource $file_loc
     * @param string          $mime
     * @return Response
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
        return new Response($stream, self::OK, ['Content-Type' => $mime]);
    }

    /**
     * creates a response for downloading a contents.
     * A contents can be, a text string, a resource, or a stream.
     *
     * @param string|StreamInterface|resource $content
     * @param string                          $filename
     * @param bool                            $attach download as attachment if true, or inline if false.
     * @param string|null                     $mime
     * @return Response
     */
    public function asDownload($content, $filename, $attach = true, $mime = null)
    {
        $type = $attach ? 'attachment' : 'inline';
        $mime = $mime ?: 'application/octet-stream';
        return new Response(
            $content,
            self::OK, [
            'Content-Disposition' => "{$type}; filename=\"{$filename}\"",
            'Content-Length'      => strlen($content),
            'Content-Type'        => $mime,
            'Cache-Control'       => 'public', // for IE8
            'Pragma'              => 'public', // for IE8
        ]);
    }
}