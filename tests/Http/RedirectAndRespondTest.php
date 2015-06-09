<?php
namespace tests\Http;

use Aura\Session\SessionFactory;
use Tuum\Http\Redirect;
use Tuum\Http\RequestHelper;
use Tuum\Http\Respond;
use Tuum\Http\ResponseHelper;
use Tuum\Http\Service\ViewData;

class RedirectAndRespondTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionFactory
     */
    public $session_factory;

    function setup()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION = [];
        $this->session_factory = new SessionFactory();
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
        $session  = $this->session_factory->newInstance([]);
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));
        $response = Redirect::forge($request)
            ->withFlashData('with', 'val1')
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
        $move = new \ReflectionMethod($session, 'moveFlash');
        $move->setAccessible(true);
        $move->invoke($session);

        /*
         * next request with the data from the previous redirection. 
         */
        $session  = $this->session_factory->newInstance([]);
        $request  = RequestHelper::createFromPath('/more/test');
        $request  = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));
        $respond  = Respond::forge($request);
        
        $refObj  = new \ReflectionObject($respond);
        $refData = $refObj->getProperty('data');
        $refData->setAccessible(true);
        /** @var ViewData $data */
        $data    = $refData->getValue($respond);

        $this->assertEquals('val1', RequestHelper::getFlash($request, 'with'));
        $this->assertEquals('message', $data->get(ViewData::MESSAGE)[0]['message']);
        $this->assertEquals('notice-msg', $data->get(ViewData::MESSAGE)[1]['message']);
        $this->assertEquals('error-msg', $data->get(ViewData::MESSAGE)[2]['message']);
        $this->assertEquals('test', $data->get(ViewData::INPUTS)['more']);
        $this->assertEquals('done', $data->get(ViewData::ERRORS)['more']);
    }
}
