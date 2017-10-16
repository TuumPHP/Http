<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Responder;

class ForbiddenController
{
    use DispatchByMethodTrait;

    /**
     * JumpController constructor.
     *
     * @param Responder $responder
     */
    public function __construct($responder)
    {
        $this->setResponder($responder);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return null|ResponseInterface
     */
    public function __invoke($request, $response)
    {
        return $this->dispatch($request, $response);
    }

    /**
     * @return ResponseInterface
     */
    public function onGet()
    {
        $session = $this->responder->session();
        /** @var ServerRequestInterface $request */
        $request = $this->getRequest()->withAttribute('_token', $session->getToken());
        $this->setRequest($request);

        return $this->view()->render('forbidden');
    }

    /**
     * @return ResponseInterface
     */
    public function onPost()
    {
        $token    = $this->getPost('_token') ?: '';
        if (!$this->session()->validateToken($token)) {
            return $this->error()->forbidden();
        }
        return $this->view()
            ->setSuccess('validated token!')
            ->render('forbidden');
    }
}