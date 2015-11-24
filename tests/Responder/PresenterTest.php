<?php
namespace tests\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder\Presenter;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewerInterface;
use Zend\Diactoros\Response;

class TestViewer implements ViewerInterface
{
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view)
    {
        return 'tested: viewer';
    }
}

class PresenterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Presenter
     */
    private $presenter;

    function setup()
    {
        $this->presenter = new Presenter();
    }

    /**
     * @test
     */
    function executes_callable()
    {
        $string = $this->presenter->call(function() { return 'tested: closure';});
        $this->assertEquals('tested: closure', $string);
    }

    /**
     * @test
     */
    function executes_ViewerInterface_object()
    {
        $presenter = $this->presenter->withRequest(ReqBuilder::createFromPath('test'), new Response('test'), SessionStorage::forge('test'), new ViewData);
        $string = $presenter->call(new TestViewer());
        $this->assertEquals('tested: viewer', $string);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function execute_string_without_resolver_throws_exception()
    {
        $this->presenter->call('bad-input');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function returning_non_callable_from_resolver_throws_exception()
    {
        $presenter = new Presenter(function() {return 'not-a-callable';});
        $presenter->call('bad-input');
    }

    /**
     * @test
     */
    function execute_using_resolver()
    {
        $presenter = new Presenter(
            function() {
                return function() {
                    return 'tested: resolver';
                };
            });
        $string = $presenter->call('any-string');
        $this->assertEquals('tested: resolver', $string);
    }
}