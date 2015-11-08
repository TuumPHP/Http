<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\SessionStorageInterface;
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
        $_SESSION = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->responder = Responder::build(
            new LocalView(),
            new ErrorBack()
        )->withResponse(new Response());
        $this->view   = $this->responder->view(RequestHelper::createFromPath('test'));
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
        $response = $this->view->asView('test-view');
        /** @var LocalView $stream */
        $stream = $response->getBody();
        $this->assertEquals('Zend\Diactoros\Response', get_class($response));
        $this->assertEquals('Zend\Diactoros\Stream', get_class($stream));
        $this->assertEquals('test-view', $response->getHeaderLine('ViewFile'));
    }

    /**
     * @test
     */
    function asContents_returns_content_file_with_content_data()
    {
        $response = $this->view->asContents('test-content', 'testing/contents');
        $this->assertEquals('testing/contents', $response->getHeaderLine('ViewFile'));
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
}