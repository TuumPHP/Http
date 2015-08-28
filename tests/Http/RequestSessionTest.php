<?php
namespace tests\Http;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Service\SessionStorage;

class RequestSessionTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var SessionStorage
     */
    private $session;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    function setup()
    {
        $_SESSION = [];
        $this->session = SessionStorage::forge('tuum-app', []);
        $this->setPhpTestFunc($this->session);
        $this->request = RequestHelper::createFromPath('test');
        $this->request = RequestHelper::withSessionMgr($this->request, $this->session);
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    /**
     * @test
     */
    function withSessionMgr_sets_session_object()
    {
        $this->assertSame($this->session, RequestHelper::getSessionMgr($this->request));
    }

    /**
     * @test
     */
    function setSession_sets_value()
    {
        $request = $this->request;
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
        $request = $this->request;
        
        // set to flash.
        RequestHelper::setFlash($request, 'more', 'tested');
        // will not see, yet. 
        $this->assertEquals(null, RequestHelper::getFlash($request, 'more'));
        // move the flash, i.e. next request.
        $this->moveFlash($this->session);
        // now you see.
        $this->assertEquals('tested', RequestHelper::getFlash($request, 'more'));
    }

    /**
     * @test
     */
    function set_array_in_setSession()
    {
        $request = $this->request;
        RequestHelper::setSession($request, ['test' => 'tested']);
        $this->assertEquals('tested', RequestHelper::getSession($request, 'test'));
        $this->assertEquals(null, RequestHelper::getSession($request, 'more'));
    }
}
