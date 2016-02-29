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
            return $this->viewUserInfo($response, $login);
        }

        return $this->viewLoginForm($response);
    }

    /**
     * @param ResponseInterface $response
     * @param string      $login
     * @return ResponseInterface
     */
    private function viewUserInfo($response, $login)
    {
        $response->getBody()->write("
            <!-- login form -->
            <form class=\"navbar-form navbar-left\" role=\"search\" action=\"/login\" method=\"post\">
                <div class=\"form-group\">
                    <input type=\"text\"name=\"login\" class=\"form-control\" placeholder=\"User: {$login}\">
                </div>
                <button type=\"submit\" class=\"btn btn-default\">Login</button>
            </form>
        ");
        return $response;
    }

    /**
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    private function viewLoginForm($response)
    {
        $response->getBody()->write('
            <!-- login form -->
            <form class="navbar-form navbar-left" role="search" action="/login" method="post">
                <div class="form-group">
                    <input type="text" name="login" class="form-control" placeholder="user name">
                </div>
                <button type="submit" class="btn btn-default">Login</button>
            </form>
        ');
        return $response;
    }
}