<?php
namespace tests\Responder;

use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Responder\ViewData;
use Zend\Diactoros\Response;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Responder
     */
    private $responder;

    function setup()
    {
        $_SESSION = [];
        class_exists(Respond::class);
        $this->responder = ResponderBuilder::withServices(
            new LocalView(),
            new ErrorBack()
        );
        $this->responder = $this->responder->withSession(SessionStorage::forge('tuum-app'))->withResponse(new Response());
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder', get_class($this->responder));
        $req = ReqBuilder::createFromPath('test');
        $this->assertEquals('Tuum\Respond\Responder\View', get_class($this->responder->view($req)));
        $this->assertEquals('Tuum\Respond\Responder\Redirect', get_class($this->responder->redirect($req)));
        $this->assertEquals('Tuum\Respond\Responder\Error', get_class($this->responder->error($req)));
    }
}
