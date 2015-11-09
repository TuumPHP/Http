<?php
namespace tests\Responder;

use Tuum\Respond\Helper\ReqBuilder;
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
        $req = ReqBuilder::createFromPath('test');
        $this->assertEquals('Tuum\Respond\Responder\View', get_class($this->responder->view($req)));
        $this->assertEquals('Tuum\Respond\Responder\Redirect', get_class($this->responder->redirect($req)));
        $this->assertEquals('Tuum\Respond\Responder\Error', get_class($this->responder->error($req)));
    }

    /**
     * @test
     */
    function withViewData_sets_viewData_which_is_passed_to_subsequent_responders()
    {
        $res = $this->responder->withViewData(function(ViewData $view) {
            $view->data('test', 'responder-tested');
            return $view;
        });
        $request  = ReqBuilder::createFromPath('/base/path');
        $response = $res->view($request)->asView('test/responder');
        /** @var LocalView $view */
        $this->assertEquals('responder-tested', $response->getBody()->__toString());
        $this->assertEquals('test/responder', $response->getHeaderLine('ViewFile'));
    }

    /**
     * @test
     */
    function View_withViewData_sets_viewData()
    {
        $request  = ReqBuilder::createFromPath('/base/path');
        $response = $this->responder
            ->view($request)
            ->withViewData(function(ViewData $view) {
                $view->data('test', 'responder-tested');
            return $view;
            })
            ->asView('test/responder');
        /** @var LocalView $view */
        $this->assertEquals('responder-tested', $response->getBody()->__toString());
        $this->assertEquals('test/responder', $response->getHeaderLine('ViewFile'));
    }
}
