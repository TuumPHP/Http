<?php
namespace tests\Service;

use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Responder\Payload;
use Tuum\Respond\Service\ViewHelper;

require_once __DIR__ . '/../autoloader.php';

class TwigViewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    function get_contents()
    {
        $twig    = Twig::forge(__DIR__ . '/twig');
        $payload = new Payload();
        $request = Respond::setPayload(ReqBuilder::createFromPath('test'), $payload);
        $helper  = ViewHelper::forge($request, null);

        $res = $twig->render('twig-text', $helper);
        $this->assertEquals('this is a text from twig.', $res);
    }

}
