<?php
namespace tests\Http;

use Tuum\Respond\Responder;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewStream;

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
        $this->session_factory = SessionStorage::forge([]);
        $this->setPhpTestFunc($this->session_factory);

        $view = ViewStream::forge('');
        $this->responder = Responder::build(
            $view,
            new ErrorView($view)
        );
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
        $session  = $this->session_factory->withStorage('test');
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session);
        $response = $this->responder->redirect($request)
            ->withFlashData('with', 'val1')
            ->with('more', 'with')
            ->withMessage('message')
            ->withAlertMsg('notice-msg')
            ->withErrorMsg('error-msg')
            ->withInputData(['more' => 'test'])
            ->withInputErrors(['more' => 'done'])
            ->toAbsoluteUri('/more/test');
        
        $this->assertEquals('/more/test', ResponseHelper::getLocation($response));

        /*
         * next request. 
         * move the flash, i.e. next request.
         */
        $session->commit();
        $stored = serialize($_SESSION);
        $_SESSION = unserialize($stored);
        
        $this->moveFlash($this->session_factory);

        /*
         * next request with the data from the previous redirection. 
         */
        $session  = $this->session_factory->withStorage('test');
        $request  = RequestHelper::createFromPath('/more/test');
        $request  = RequestHelper::withSessionMgr($request, $session);
        $respond  = $this->responder->view($request);
        
        $refObj  = new \ReflectionObject($respond);
        $refData = $refObj->getProperty('data');
        $refData->setAccessible(true);
        /** @var ViewData $data */
        $data    = $refData->getValue($respond);

        $this->assertEquals('val1', RequestHelper::getFlash($request, 'with'));
        $this->assertEquals('with', $data->get(ViewData::DATA)['more']);
        $this->assertEquals('message', $data->get(ViewData::MESSAGE)[0]['message']);
        $this->assertEquals('notice-msg', $data->get(ViewData::MESSAGE)[1]['message']);
        $this->assertEquals('error-msg', $data->get(ViewData::MESSAGE)[2]['message']);
        $this->assertEquals('test', $data->get(ViewData::INPUTS)['more']);
        $this->assertEquals('done', $data->get(ViewData::ERRORS)['more']);
    }
}
