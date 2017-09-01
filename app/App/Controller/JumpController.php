<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Responder;

class JumpController
{
    /**
     * @var Responder
     */
    private $responder;

    /**
     * JumpController constructor.
     *
     * @param Responder $responder
     */
    public function __construct($responder)
    {
        $this->responder = $responder;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke($request, $response)
    {
        if ($request->getMethod() === 'POST') {
            return $this->onPost($request, $response);
        }

        return $this->onGet($request, $response);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    private function onGet($request, $response)
    {
        $this->responder
            ->getViewData()
            ->setSuccess('try jump to another URL. ')
            ->setData('jumped', 'text in control')
            ->setData('date', (new \DateTime('now'))->format('Y-m-d'));

        return $this->responder
            ->view($request, $response)
            ->render('jump');
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    private function onPost($request, $response)
    {
        $this->responder->getViewData()
            ->setError('redirected back!')
            ->setInput($request->getParsedBody())
            ->setInputErrors([
                'jumped' => 'redirected error message',
                'date'   => 'your date',
                'gender' => 'your gender',
                'movie'  => 'selected movie',
                'happy'  => 'be happy!'
            ]);

        return $this->responder
            ->redirect($request, $response)
            ->toPath('jump');
    }
}
