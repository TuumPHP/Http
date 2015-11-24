<?php
namespace Tuum\Respond\Responder;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Service\ViewerInterface;

class Presenter extends AbstractWithViewData
{
    /**
     * resolves a presenter of string to a callable.
     * signature of the callable is:
     * $resolver(ServerRequestInterface $req, ResponseInterface $res, ViewData $data);
     *
     * @var callable|null
     */
    private $resolver;

    /**
     * Presenter constructor.
     *
     * @param null|callable $resolver
     */
    public function __construct($resolver = null)
    {
        $this->resolver = $resolver;
    }

    /**
     * calls the presenter to create a view to respond.
     *
     * @param callable|ViewerInterface|string $presenter
     * @return ResponseInterface
     */
    public function call($presenter)
    {
        if ($presenter instanceof ViewerInterface) {
            return $this->execCallable([$presenter, 'withView']);
        }
        if (is_callable($presenter)) {
            return $this->execCallable($presenter);
        }
        if (!$resolver = $this->resolver) {
            throw new \BadMethodCallException('set resolver to Presenter!');
        }
        return $this->execCallable($resolver($presenter));
    }

    /**
     * @param callable $callable
     * @return ResponseInterface
     */
    private function execCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException;
        }
        return call_user_func($callable, $this->request, $this->response, $this->data);
    }
}