<?php
namespace tests\Http;

use Aura\Session\SessionFactory;
use Tuum\Respond\RequestHelper;

class RequestSessionTest extends \PHPUnit_Framework_TestCase
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
     * @test
     */
    function withSessionMgr_sets_session_object()
    {
        $request = RequestHelper::createFromPath('test');
        $session = $this->session_factory->newInstance([]);
        $segment = $session->getSegment('tuum-app');
        $request = RequestHelper::withSessionMgr($request, $segment);
        $this->assertSame($segment, RequestHelper::getSessionMgr($request));
    }

    /**
     * @test
     */
    function setSession_sets_value()
    {
        $request = RequestHelper::createFromPath('test');
        $session = $this->session_factory->newInstance([]);
        $request = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));

        $this->assertEquals(null, RequestHelper::getSession($request, 'test'));
        RequestHelper::setSession($request, 'test', 'tested');
        $this->assertEquals('tested', RequestHelper::getSession($request, 'test'));
        $this->assertEquals('tested', $_SESSION['tuum-app']['test']);
    }

    /**
     * @test
     */    
    function setFlash_sets_value()
    {
        $request = RequestHelper::createFromPath('test');
        $session = $this->session_factory->newInstance([]);
        $request = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));
        
        // set to flash.
        RequestHelper::setFlash($request, 'more', 'tested');
        // will not see, yet. 
        $this->assertEquals(null, RequestHelper::getFlash($request, 'more'));
        // move the flash, i.e. next request. 
        $move = new \ReflectionMethod($session, 'moveFlash');
        $move->setAccessible(true);
        $move->invoke($session);
        // now you see. 
        $this->assertEquals('tested', RequestHelper::getFlash($request, 'more'));
    }
}
