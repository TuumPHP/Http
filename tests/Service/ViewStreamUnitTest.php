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
        $this->assertEquals($this->string, $this->view->getContents());
        $this->assertEquals($this->string, (string) $this->view);
    }

    /**
     * @test
     */
    function getSize_returns_the_size()
    {
        $this->assertEquals(strlen($this->string), $this->view->getSize());
    }

    /**
     * @test
     */
    function seek_rewind_and_read_will_get_part_of_string()
    {
        $size = $this->view->getSize();
        $mid  = (int) ($size/2);
        $last = $size - $mid;
        $this->view->rewind();
        $this->assertEquals(substr($this->string, 0, $mid), $this->view->read($mid));
        $this->assertEquals($mid, $this->view->tell());
        $this->assertEquals(substr($this->string, $mid), $this->view->read($last));
        $this->assertEquals('', $this->view->read($last));
        $this->view->rewind();
        $this->assertEquals(substr($this->string, 0, $mid), $this->view->read($mid));
    }

    /**
     * @test
     */
    function eof_checks_end_of_file()
    {
        $this->assertFalse($this->view->eof());
        $this->view->getContents();
        $this->assertTrue($this->view->eof());
        $this->view->rewind();
        $this->assertFalse($this->view->eof());
    }

    /**
     * @test
     */
    function write_string()
    {
        $extra = 'written';
        $this->view->write($extra);
        $this->assertEquals($this->string.$extra, $this->view->getContents());

        $this->view->rewind();
        $this->view->write($extra);
        $this->assertEquals('writtenntwritten', $this->view->getContents());
    }

    /**
     * @test
     */
    function getMeta()
    {
        $meta = $this->view->getMetadata();
        $this->assertTrue(is_array($meta));
        $uri = $meta['uri']; // assume always here.
        $this->assertEquals($uri, $this->view->getMetadata('uri'));
    }
}