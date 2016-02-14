<?php
namespace tests\Http;

use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Responder\ViewData;
use Zend\Diactoros\Response;

class RedirectAndRespondTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var SessionStorage
     */
    public $session_factory;

    /**
     * @var Responder
     */
    public $responder;

    function setup()
    {
        $_SESSION = [];
        $this->session_factory = SessionStorage::forge('test');
        $this->setPhpTestFunc($this->session_factory);

        $view = TuumViewer::forge('');
        $this->responder = ResponderBuilder::withView($view)
            ->withResponse(new Response())
            ->withSession($this->session_factory);
    }

    /**
     * create a redirect response with various data (error message, input data, input errors). 
     * 
     * the subsequent request will create a respond with the data set in the previous redirect response. 
     */
    function test()
    {
        /*
         * a redirect response with various data.
         */
        $request  = ReqBuilder::createFromPath('/path/test');
        $view     = $this->responder->getViewData()
            ->setData('more', 'with')
            ->setSuccess('message')
            ->setAlert('notice-msg')
            ->setError('error-msg')
            ->setInputData(['more' => 'test'])
            ->setInputErrors(['more' => 'done'])
        ;
        $response = $this->responder->redirect($request)
            ->toAbsoluteUri('/more/test');
        $this->responder->session()
            ->setFlash('with', 'val1');
        
        $this->assertEquals('/more/test', ResponseHelper::getLocation($response));

        /*
         * next request. 
         * move the flash, i.e. next request.
         */
        $this->session_factory->commit();
        $stored = serialize($_SESSION);
        $_SESSION = unserialize($stored);
        
        $this->moveFlash($this->session_factory);

        /*
         * next request with the data from the previous redirection. 
         */
        $session  = $this->session_factory->withStorage('test');
        $responder= $this->responder->withSession($session);
        
        $data    = $view;

        $this->assertEquals('val1', $responder->session()->getFlash('with'));
        $this->assertEquals('with', $data->getData()['more']);
        $this->assertEquals('message', $data->getMessages()[0]['message']);
        $this->assertEquals('notice-msg', $data->getMessages()[1]['message']);
        $this->assertEquals('error-msg', $data->getMessages()[2]['message']);
        $this->assertEquals('test', $data->getInputData()['more']);
        $this->assertEquals('done', $data->getInputErrors()['more']);
    }
}
