<?php
namespace tests\Responder;

use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\RawPhp;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

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
        $this->responder = Responder::forge(
            Builder::forge('test')
            ->setRenderer(new RawPhp('none'))
        )->withResponse(new Response());
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
