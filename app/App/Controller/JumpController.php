<?php
namespace App\App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Controller\DispatchByMethodTrait;
use Tuum\Respond\Responder;

class JumpController
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
    protected function onGet()
    {
        return $this->view()
            ->setSuccess('try jump to another URL. ')
            ->setData('jumped', 'text in control')
            ->setData('date', (new \DateTime('now'))->format('Y-m-d'))
            ->render('jump');
    }

    /**
     * @return ResponseInterface
     */
    protected function onPost()
    {
        return $this->redirect()
            ->setError('redirected back!')
            ->setInput($this->getPost())
            ->setInputErrors([
                'jumped' => 'redirected error message',
                'date'   => 'your date',
                'gender' => 'your gender',
                'movie'  => 'selected movie',
                'happy'  => 'be happy!'
            ])->toReferrer();
    }
}
