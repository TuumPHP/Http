<?php
namespace App\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Responder;

class LoginPresenter implements PresenterInterface
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * @param Dispatcher $app
     * @return UploadViewer
     */
    public static function forge($app)
    {
        $self            = new self;
        $self->responder = $app->get('responder');

        return $self;
    }

    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewDataInterface      $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewData)
    {
        $login = $this->responder->session()->get('login.name');
        if ($login) {
            return $this->responder->view($request, $response)
                ->render('layouts/UserHeaderLogIn', ['login' => $login]);
        }

        return $this->responder->view($request, $response)
            ->render('layouts/UserHeaderLoginForm');
    }
}