<?php
namespace tests\Service;

use Tuum\Respond\Service\ViewStream;
use Tuum\View\Renderer;

require_once __DIR__ . '/../autoloader.php';

class ViewStreamUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    public $string;

    /**
     * @var ViewStream
     */
    public $view;

    function setup()
    {
        $this->string = 'a content';
        $render = $this->getMockBuilder(Renderer::class)->disableOriginalConstructor()->getMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $render->method('render')->willReturn($this->string);
        /** @noinspection PhpParamsInspection */
        $this->view = new ViewStream($render);
    }

    /**
     * @test
     */
    function isSeekable_isReadable_isWritable_are_all_true()
    {
        $this->assertTrue($this->view->isSeekable());
        $this->assertTrue($this->view->isReadable());
        $this->assertTrue($this->view->isWritable());
    }
    
    /**
     * @test
     */
    function getContents_return_the_string()
    {
        $view = $this->view->withView('some');
        $this->assertEquals($this->string, $view->getContents());
        $this->assertEquals($this->string, (string) $view);
    }

    /**
     * @test
     */
    function getSize_returns_the_size()
    {
        $view = $this->view->withView('some');
        $this->assertEquals(strlen($this->string), $view->getSize());
    }

    /**
     * @test
     */
    function seek_rewind_and_read_will_get_part_of_string()
    {
        $view = $this->view->withView('some');
        $size = $view->getSize();
        $mid  = (int) ($size/2);
        $last = $size - $mid;
        $view->rewind();
        $this->assertEquals(substr($this->string, 0, $mid), $view->read($mid));
        $this->assertEquals($mid, $view->tell());
        $this->assertEquals(substr($this->string, $mid), $view->read($last));
        $this->assertEquals('', $view->read($last));
        $view->rewind();
        $this->assertEquals(substr($this->string, 0, $mid), $view->read($mid));
    }

    /**
     * @test
     */
    function eof_checks_end_of_file()
    {
        $view = $this->view->withView('some');
        $this->assertFalse($view->eof());
        $view->getContents();
        $this->assertTrue($view->eof());
        $view->rewind();
        $this->assertFalse($view->eof());
    }

    /**
     * @test
     */
    function write_string()
    {
        $view = $this->view->withView('some');
        $extra = 'written';
        $view->write($extra);
        $this->assertEquals($this->string.$extra, $view->getContents());

        $view->rewind();
        $view->write($extra);
        $this->assertEquals('writtenntwritten', $view->getContents());
    }

    /**
     * @test
     */
    function getMeta()
    {
        $view = $this->view->withView('some');
        $meta = $view->getMetadata();
        $this->assertTrue(is_array($meta));
        $uri = $meta['uri']; // assume always here.
        $this->assertEquals($uri, $view->getMetadata('uri'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */    
    function close_will_make_stream_unusable()
    {
        $view = $this->view->withView('some');
        $this->assertEquals($this->string, $view->getContents());
        $view->close();
        $this->assertEquals($this->string, $view->getContents());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function detach_will_return_fp_and_make_stream_unusable()
    {
        $view = $this->view->withView('some');
        $fp   = $view->detach();
        rewind($fp);
        $this->assertEquals($this->string, stream_get_contents($fp));
        $this->assertEquals($this->string, $view->getContents());
    }
}