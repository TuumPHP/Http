<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Controller\AbstractPresenter;
use Tuum\Respond\Controller\PresentByContentTrait;
use Tuum\Respond\Responder;

class LoginPresenter extends AbstractPresenter
{
    use PresentByContentTrait;

    /**
     * LoginPresenter constructor.
     *
     * @param Responder $responder
     */
    public function __construct($responder)
    {
        $this->setResponder($responder);
    }

    /**
     * renders $view and returns a new $response.
     *
     * @return ResponseInterface
     */
    public function html()
    {
        $login = $this->responder->session()->get('login.name');
        if ($login) {
            return $this->view()
                ->render('layouts/UserHeaderLogIn', ['login' => $login]);
        }

        return $this->view()
            ->render('layouts/UserHeaderLoginForm');
    }
}