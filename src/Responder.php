<?php
namespace Tuum\Respond;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ContainerInterface $container
     * @return Responder
     */
    public static function forge(ContainerInterface $container)
    {
        return new self($container);
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
        return $this->container->get(View::class)->start($request, $this);
    }

    public function redirect(ServerRequestInterface $request): Redirect
    {
        return $this->container->get(Redirect::class)->start($request, $this);
    }

    public function error(ServerRequestInterface $request): Error 
    {
        return $this->container->get(Error::class)->start($request, $this);
    }

    /**
     * @param ServerRequestInterface $request
     * @return SessionStorageInterface
     */
    public function session(ServerRequestInterface $request): SessionStorageInterface
    {
        return Respond::getSession($request);
    }

    public function getPayload(ServerRequestInterface $request): PayloadInterface
    {
        return Respond::getPayload($request);
    }
    
    public function setUpRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $session = $this->container->get(SessionStorageInterface::class);
        $request = Respond::setSession($request, $session);
        $request = Respond::setPayload($request, $session->getPayload());

        return $request;
    }

    public function savePayload(ServerRequestInterface $request): void
    {
        $this->session($request)->savePayload($this->getPayload($request));
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
    
    public function makeResponse(int $code, $contents, array $header = []): ResponseInterface
    {
        if ($this->container->has(ResponseFactoryInterface::class)) {
            $factory = $this->container->get(ResponseFactoryInterface::class);
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
        return$this->container->get(NamedRoutesInterface::class);
    }

    public function makeStream($contents): StreamInterface
    {
        if ($this->container->has(StreamFactoryInterface::class)) {
            $factory = $this->container->get(StreamFactoryInterface::class);
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
            rewind($contents);
            $stream->write(stream_get_contents($contents));
            return $stream;
        }

        throw new \InvalidArgumentException('contents not a string nor a resource');
    }

    public function resolve(string $id)
    {
        if ($this->container->has($id)) {
            return $this->container->get($id);
        }
        return null;
    }
    
    public function resolvable(string $id): bool
    {
        if ($this->container) {
            return $this->container->has($id);
        }
        return false;
    }
}