<?php
namespace tests\Service;

use Tuum\Respond\Service\ViewData;
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
        $renderer = $view->modRenderer(function($renderer) {
            return $renderer;
        });
        $this->assertEquals('Tuum\View\Renderer', get_class($renderer));
    }

    /**
     * @test
     */
    function check_view_data()
    {
        $vd   = new ViewData();
        $view = $this->view->withView('simple-text', $vd);
        $data = $view->modRenderer(
            /**
             * @return array
             */
            function() {
                /** @noinspection PhpUndefinedFieldInspection */
                return $this->view_data;
        });
        $this->assertTrue(is_array($data));
        $this->assertEquals('Tuum\Form\DataView', get_class($data['view']));
    }
}
