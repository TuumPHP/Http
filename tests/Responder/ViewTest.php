<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Helper\ResponderBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Interfaces\PresenterInterface;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Zend\Diactoros\Response;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var Responder
     */
    public $responder;

    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var View
     */
    private $view;

    function setup()
    {
        $_SESSION      = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->responder = ResponderBuilder::withServices(
            new LocalView(),
            new RenderErrorBack()
        )->withResponse(new Response())
            ->withSession($this->session);
        $this->view      = $this->responder->view(ReqBuilder::createFromPath('test'));
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder\View', get_class($this->view));
    }

    /**
     * @test
     */
    function view_returns_viewStream()
    {
        $response = $this->view->render('test-view');
        /** @var LocalView $stream */
        $stream = $response->getBody();
        $this->assertEquals('Zend\Diactoros\Response', get_class($response));
        $this->assertEquals('Zend\Diactoros\Stream', get_class($stream));
        $this->assertEquals('test-view', $response->getHeaderLine('ViewFile'));
    }

    /**
     * @test
     */
    function asContents_uses_default_content_file()
    {
        $this->view->content_view = 'default/contents';
        $response                 = $this->view->asContents('test-content');
        $this->assertEquals('default/contents', $response->getHeaderLine('ViewFile'));
    }

    /**
     * @test
     */
    function asFileContents_returns_fp_as_stream()
    {
        $fp = fopen('php://memory', 'wb+');
        fwrite($fp, 'test/resource');

        $response = $this->view->asFileContents($fp, 'mime/test');
        $this->assertSame('test/resource', $response->getBody()->__toString());
        $this->assertEquals('mime/test', $response->getHeader('Content-Type')[0]);
    }

    /**
     * @test
     */
    function executes_callable()
    {
        $string = $this->view->call(function () {
            return 'tested: closure';
        });
        $this->assertEquals('tested: closure', $string);
    }

    /**
     * @test
     */
    function executes_ViewerInterface_object()
    {
        $stub = $this->getMockBuilder(PresenterInterface::class)->getMock();
        $stub->method('__invoke')->willReturn('tested: mock');
        $string = $this->view->call($stub);
        $this->assertEquals('tested: mock', $string);
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function execute_string_without_resolver_throws_exception()
    {
        $this->view->call('bad-input');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function returning_non_callable_from_resolver_throws_exception()
    {
        $view = new View(new LocalView(), null, function () {
            return 'not-a-callable';
        });
        $view->call('bad-input');
    }

    /**
     * @test
     */
    function execute_using_resolver()
    {
        $view   = new View(
            new LocalView(), null,
            function () {
                return function () {
                    return 'tested: resolver';
                };
            });
        $string = $view->call('any-string');
        $this->assertEquals('tested: resolver', $string);
    }
}