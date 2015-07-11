<?php
namespace tests\Service;

use Tuum\Respond\Service\ViewStream;

require_once __DIR__ . '/../autoloader.php';

class ViewStreamFuncTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewStream
     */
    public $view;

    function setup()
    {
        $this->view = ViewStream::forge(__DIR__.'/views');
    }

    /**
     * @test
     */
    function get_contents()
    {
        $view = $this->view->withView('simple-text');
        $this->assertEquals('this is a simple text.', $view->getContents());
    }

    /**
     * @test
     */
    function mod_render()
    {
        $view = $this->view->withView('simple-text');
        $view->modRenderer(function($renderer) {
            $this->assertEquals('Tuum\View\Renderer', get_class($renderer));
        });
    }
}
