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
        $this->responder
            ->getViewData()
            ->setSuccess('try jump to another URL. ')
            ->setData('jumped', 'text in control')
            ->setData('date', (new \DateTime('now'))->format('Y-m-d'));

        return $this->view()->render('jump');
    }

    /**
     * @return ResponseInterface
     */
    protected function onPost()
    {
        $this->getViewData()
            ->setError('redirected back!')
            ->setInput($this->getPost())
            ->setInputErrors([
                'jumped' => 'redirected error message',
                'date'   => 'your date',
                'gender' => 'your gender',
                'movie'  => 'selected movie',
                'happy'  => 'be happy!'
            ]);

        return $this->redirect()->toPath('jump');
    }
}
