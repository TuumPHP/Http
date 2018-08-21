<?php
namespace tests\Service;

use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Service\TwigViewer;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewHelper;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class TwigViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    function get_contents()
    {
        $twig = Twig::forge(__DIR__ . '/twig');
        $view = new ViewData();
        $helper = ViewHelper::forge(ReqBuilder::createFromPath('test'), new Response(), $view, null);

        $res = $twig->render('twig-text', $helper);
        $this->assertEquals('this is a text from twig.', $res);
    }

}
