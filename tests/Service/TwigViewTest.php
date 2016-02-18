<?php
namespace tests\Service;

use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Service\TwigViewer;
use Tuum\Respond\Responder\ViewData;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class TwigViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function get_contents()
    {
        $twig = TwigViewer::forge(__DIR__ . '/twig');
        $view = new ViewData();

        $res = $twig->__invoke(ReqBuilder::createFromPath('test'), new Response(), 'twig-text', $view);
        $this->assertEquals('this is a text from twig.', $res->getBody()->__toString());
    }

}
