<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewData;

class ResponderTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var Responder
     */
    private $responder;

    function setup()
    {
        class_exists(Respond::class);
        $this->responder = Responder::build(
            new LocalView(),
            new ErrorBack()
        );
        $this->responder = $this->responder->withSession(SessionStorage::forge('tuum-app'));
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder', get_class($this->responder));
    }

    /**
     * @test
     */
    function with_sets_viewData_which_is_passed_to_subsequent_responders()
    {
        $res = $this->responder->viewData(function(ViewData $view) {
            $view->setRawData('responder-with', 'tested');
            return $view;
        });
        $request  = RequestHelper::createFromPath('/base/path');
        $response = $res->view($request)->asView('test/responder');
        /** @var LocalView $view */
        $view     = $response->getBody();
        $this->assertEquals('test/responder', $view->view_file);
        $this->assertEquals('tested', $view->data->getRawData()['responder-with']);
    }

    /**
     * @test
     */
    function Respond_with_alters_responder()
    {
        $request  = RequestHelper::createFromPath('/base/path');
        $request  = RequestHelper::withResponder($request, $this->responder);
        Respond::with($request, function(ViewData $view) {
            $view->setRawData('respond-with', 'tested');
            return $view;
        });
        $response = Respond::view($request)->asView('test/respond');
        /** @var LocalView $view */
        $view     = $response->getBody();
        $this->assertEquals('test/respond', $view->view_file);
        $this->assertEquals('tested', $view->data->getRawData()['respond-with']);
    }
}
