<?php
namespace Tuum\Respond;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Tuum\Respond\Interfaces\NamedRoutesInterface;
use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\SessionStorageInterface;

class Responder
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Builder $builder
     * @return Responder
     */
    public static function forge(Builder $builder)
    {
        return new self($builder);
    }

    /**
     * set response object when omitting $response when calling
     * responders, such as:
     * Respond::view($request);
     *
     * responders will return $response using this object.
     *
     * @api
     * @param ResponseInterface $response
     * @return Responder
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    public function view(ServerRequestInterface $request): View
    {
        return $this->builder->getView()->start($request, $this);
    }

    public function redirect(ServerRequestInterface $request): Redirect
    {
        return $this->builder->getRedirect()->start($request, $this);
    }

    public function error(ServerRequestInterface $request): Error 
    {
        return $this->builder->getError()->start($request, $this);
    }

    public function session(): SessionStorageInterface
    {
        return $this->builder->getSessionStorage();
    }

    public function getPayload(ServerRequestInterface $request): ?PayloadInterface
    {
        return Respond::getPayload($request);
    }
    
    public function setPayload(ServerRequestInterface $request): ServerRequestInterface
    {
        if (Respond::getPayload($request)) {
            return $request;
        }
        return $request->withAttribute(PayloadInterface::class, $this->session()->getPayload());
    }

    public function savePayload(ServerRequestInterface $request): void
    {
        $this->session()->savePayload($this->getPayload($request));
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
    
    public function makeResponse(int $code, $contents, array $header = []): ResponseInterface
    {
        if ($factory = $this->builder->getResponseFactory()) {
            $response = $factory->createResponse($code);
        } else {
            $response = $this->getResponse();
            $response = $response->withStatus($code);
        }
        if (!$response) {
            throw new \BadMethodCallException('no response nor response-factory');
        }
        foreach ($header as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $stream   = $this->makeStream($contents);
        $response = $response->withBody($stream);
        
        return $response;
    }

    public function routes(): NamedRoutesInterface
    {
        return$this->builder->getNamedRoutes();
    }

    public function makeStream($contents): StreamInterface
    {
        if ($factory = $this->builder->getStreamFactory()) {
            if (is_string($contents)) {
                return $factory->createStream($contents);
            }
            if (is_resource($contents)) {
                return $factory->createStreamFromResource($contents);
            }
        }
        $stream = $this->getResponse()->getBody();
        $stream->rewind();
        if (is_string($contents)) {
            $stream->write($contents);
            return $stream;
        }
        if (is_resource($contents)) {
            $stream->write(stream_get_contents($contents));
            return $stream;
        }

        throw new \InvalidArgumentException('contents not a string nor a resource');
    }
}