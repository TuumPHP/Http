<?php
namespace tests\Http;

use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\SessionStorage;

class RequestSessionTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var SessionStorage
     */
    public $session_factory;

    function setup()
    {
        $_SESSION = [];
        $this->session_factory = SessionStorage::forge([]);
        $this->setPhpTestFunc($this->session_factory);
    }

    /**
     * @test
     */
    function withSessionMgr_sets_session_object()
    {
        $request = RequestHelper::createFromPath('test');
        $session = $this->session_factory->withStorage('tuum-app');
        $segment = $session;
        $request = RequestHelper::withSessionMgr($request, $segment);
        $this->assertSame($segment, RequestHelper::getSessionMgr($request));
    }

    /**
     * @test
     */
    function setSession_sets_value()
    {
        $request = RequestHelper::createFromPath('test');
        $session = $this->session_factory->withStorage('tuum-app');
        $request = RequestHelper::withSessionMgr($request, $session);

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
        $session = $this->session_factory->withStorage('tuum-app');
        $request = RequestHelper::withSessionMgr($request, $session);
        
        // set to flash.
        RequestHelper::setFlash($request, 'more', 'tested');
        // will not see, yet. 
        $this->assertEquals(null, RequestHelper::getFlash($request, 'more'));
        // move the flash, i.e. next request.
        $this->moveFlash($this->session_factory);
        // now you see.
        $this->assertEquals('tested', RequestHelper::getFlash($request, 'more'));
    }
}
