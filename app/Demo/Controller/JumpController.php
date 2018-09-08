<?php
namespace App\Demo\Controller;

use Psr\Http\Message\ResponseInterface;
use Tuum\Respond\Controller\AbstractRequestHandler;
use Tuum\Respond\Responder;

class JumpController extends AbstractRequestHandler
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
    protected function onGet()
    {
        return $this->view()
            ->setSuccess('try jump to another URL. ')
            ->setData('jumper', 'text in control')
            ->setData('date', (new \DateTime('now'))->format('Y-m-d'))
            ->setData('movie', ['3'])
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
