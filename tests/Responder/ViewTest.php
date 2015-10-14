<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\SessionStorageInterface;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewStreamInterface;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    use TesterTrait;

    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var View
     */
    private $view;

    /**
     * @var ViewStreamInterface
     */
    private $stream;

    function setup()
    {
        $_SESSION = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->stream = new LocalView();
        $this->view   = new View($this->stream);
        $this->view   = $this->view
            ->withSession($this->session)
            ->withRequest(RequestHelper::createFromPath('test'));
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
        $this->assertEquals('tests\Responder\LocalView', get_class($stream));
        $this->assertEquals('test-view', $stream->view_file);
    }

    /**
     * @test
     */
    function asContents_returns_content_file_with_content_data()
    {
        $response = $this->view->asContents('test-content', 'testing/contents');
        /** @var LocalView $stream */
        $stream = $response->getBody();
        $this->assertEquals('test-content', $stream->data->get(ViewData::DATA)['contents']);
        $this->assertEquals('testing/contents', $stream->view_file);
    }

    /**
     * @test
     */
    function asContents_uses_default_content_file()
    {
        $this->view->content_view = 'default/contents';
        $response                 = $this->view->asContents('test-content');
        /** @var LocalView $stream */
        $stream = $response->getBody();
        $this->assertEquals('test-content', $stream->data->get(ViewData::DATA)['contents']);
        $this->assertEquals('default/contents', $stream->view_file);
    }

    /**
     * @test
     */
    function asFileContents_returns_fp_as_stream()
    {
        $fp = fopen('php://memory', 'r+');
        $response = $this->view->asFileContents($fp, 'mime/test');
        $this->assertSame($fp, $response->getBody()->detach());
        $this->assertEquals('mime/test', $response->getHeader('Content-Type')[0]);
    }
}