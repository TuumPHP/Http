<?php
namespace App\Demo;

use App\Demo\Controller\ForbiddenController;
use App\Demo\Controller\JumpController;
use App\Demo\Controller\UploadController;
use Koriym\Printo\Printo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;

class Routes implements \IteratorAggregate
{
    private $routes = [];

    /**
     * @var Responder
     */
    private $responder;

    public function __construct(Responder $responder)
    {
        $this->responder = $responder;
        $this->setup();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    private function setup()
    {
        $this->routes = [
            'home'      => ['/', [$this, 'onHome']],
            'info'      => ['/info', [$this, 'onInfo']],
            'contents'  => [
                '/content',
                function (ServerRequestInterface $request) {
                    return $this->responder->view($request)
                        ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>');
                }
            ],
            'throw'     => [
                '/throw',
                function () {
                    throw new \Exception('always throws exception');
                }
            ],
            'objGraph'  => ['/objGraph',
                function (ServerRequestInterface $request) {
                    return Respond::view($request)
                        ->asContents((new Printo($this->responder)));
                }],
            'login'     => ['/login', [$this, 'onLogin']],
            'forbidden' => ['/forbidden', ForbiddenController::class],
            'jump'      => ['/jump', JumpController::class],
            'upload'    => ['/upload', UploadController::class],
        ];
    }

    public function onHome(ServerRequestInterface $request): ResponseInterface
    {
        $responder = $this->responder;
        if (!$responder->session()->get('first.time')) {
            $responder->session()->set('first.time', true);
            $responder->getPayload($request)
                ->setSuccess('Thanks for downloading Tuum/Respond.');
        }

        return $responder
            ->view($request)
            ->render('index');
    }

    public function onInfo(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responder->view($request)
            ->asObContents(function () {
                phpinfo();
            });
    }

    public function onLogin(ServerRequestInterface $request): ResponseInterface
    {
        $responder = $this->responder;
        $post      = $request->getParsedBody();
        $payload   = $responder->getPayload($request);
        if (isset($post['logout'])) {
            $responder->session()->set('login.name', null);
            $payload->setSuccess('logged out');
        } elseif (isset($post['login'])) {
            if ($post['login']) {
                $responder->session()->set('login.name', $post['login']);
                $payload->setSuccess('logged as: ' . $post['login']);
            } else {
                $payload->setAlert('enter login name');
            }
        }

        return $responder
            ->redirect($request)
            ->toPath('/');
    }
}