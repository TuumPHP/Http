<?php
namespace App\App\Controller;

use App\App\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Controller\PresentByContentTrait;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Responder;

class LoginPresenter implements PresenterInterface
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