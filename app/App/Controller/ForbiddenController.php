<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Controller\AbstractController;
use Tuum\Respond\Responder;

class ForbiddenController extends AbstractController
{
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
     * @return ResponseInterface
     */
    public function onGet()
    {
        $session = $this->getResponder()->session();
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