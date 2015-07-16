<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ResponseHelper
{
    /**
     * creates a new $response.
     *
     * @param StreamInterface|string|resource|object $input
     * @param int                                    $status
     * @param array                                  $header
     * @return Response|ResponseInterface
     */
    public static function createResponse($input, $status = 200, array $header = [])
    {
        $stream = self::makeStream($input);

        return new Response(
            $stream,
            $status,
            $header
        );
    }

    /**
     * compose a $response, or creates a new $response if a $response is not set.
     *
     * @param ResponseInterface|null                 $response
     * @param StreamInterface|string|resource|object $input
     * @param int                                    $status
     * @param array                                  $header
     * @return ResponseInterface
     */
    public static function composeResponse($response, $input, $status = 200, array $header = [])
    {
        if (!$response) {
            return self::createResponse($input, $status, $header);
        }
        $response = $response->withStatus($status);
        foreach ($header as $name => $val) {
            $response = $response->withHeader($name, $val);
        }
        if (is_string($input)) {
            $response->getBody()->write($input);
            return $response;
        }

        return $response->withBody(self::makeStream($input));
    }

    /**
     * create a new stream object based on various $input.
     * $input can be:
     * - an object implementing StreamInterface,
     * - a string,
     * - a resource,
     * - an object implementing __toString method.
     *
     * @param string|StreamInterface|resource|mixed $input
     * @return StreamInterface
     */
    private static function makeStream($input)
    {
        if ($input instanceof StreamInterface) {
            return $input;

        } elseif (is_string($input)) {
            $stream = new Stream('php://memory', 'wb+');
            $stream->write($input);

            return $stream;

        } elseif (is_resource($input)) {
            return new Stream($input);

        } elseif (is_object($input) && method_exists($input, '__toString')) {
            $stream = new Stream('php://memory', 'wb+');
            $stream->write($input->__toString());

            return $stream;
        }
        throw new \InvalidArgumentException;
    }

    /**
     * Is this response successful?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isOk($response)
    {
        return $response->getStatusCode() === 200;
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isRedirect($response)
    {
        return in_array($response->getStatusCode(), [301, 302, 303, 307]);
    }

    /**
     * @param ResponseInterface $response
     * @param int               $from
     * @param int               $to
     * @return bool
     */
    private static function isStatusCodeInRange($response, $from, $to)
    {
        return $response->getStatusCode() >= $from && $response->getStatusCode() < $to;
    }

    /**
     * Is this response a client error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isInformational($response)
    {
        return self::isStatusCodeInRange($response, 100, 200);
    }

    /**
     * Is this response a success?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isSuccess($response)
    {
        return self::isStatusCodeInRange($response, 200, 300);
    }

    /**
     * Is this response a redirection?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isRedirection($response)
    {
        return self::isStatusCodeInRange($response, 300, 400);
    }

    /**
     * Is this response a client error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isClientError($response)
    {
        return self::isStatusCodeInRange($response, 400, 500);
    }

    /**
     * Is this response a server error?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isServerError($response)
    {
        return self::isStatusCodeInRange($response, 500, 600);
    }

    /**
     * Is this response an error, (either of client or server error)?
     *
     * @param ResponseInterface $response
     * @return bool
     */
    public static function isError($response)
    {
        return self::isStatusCodeInRange($response, 400, 600);
    }

    /**
     * gets a header's Location value if set.
     *
     * @param ResponseInterface $response
     * @return string
     */
    public static function getLocation($response)
    {
        $locations = $response->getHeader('Location');
        if (!empty($locations)) {
            return $locations[0];
        }

        return null;
    }

    /**
     * emits the response.
     *
     * @param ResponseInterface $response
     */
    public static function emit($response)
    {
        self::emitHeaders($response);
        echo $response->getBody()->__toString();
    }

    /**
     * @param ResponseInterface $response
     */
    public static function emitHeaders($response)
    {
        header(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));
        foreach ($response->getHeaders() as $header => $values) {
            $first = true;
            foreach ($values as $value) {
                header(sprintf(
                    '%s: %s',
                    $header,
                    $value
                ), $first);
                $first = false;
            }
        }
    }
}