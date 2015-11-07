<?php
namespace tests\Responder;

use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewData;
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
        $this->responder = Responder::build(
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
        $req = RequestHelper::createFromPath('test');
        $this->assertEquals('Tuum\Respond\Responder\View', get_class($this->responder->view($req)));
        $this->assertEquals('Tuum\Respond\Responder\Redirect', get_class($this->responder->redirect($req)));
        $this->assertEquals('Tuum\Respond\Responder\Error', get_class($this->responder->error($req)));
    }

    /**
     * @test
     */
    function with_sets_viewData_which_is_passed_to_subsequent_responders()
    {
        $res = $this->responder->viewData(function(ViewData $view) {
            $view->setData('test', 'responder-tested');
            return $view;
        });
        $request  = RequestHelper::createFromPath('/base/path');
        $response = $res->view($request)->asView('test/responder');
        /** @var LocalView $view */
        $this->assertEquals('responder-tested', $response->getBody()->__toString());
        $this->assertEquals('test/responder', $response->getHeaderLine('ViewFile'));
    }
}