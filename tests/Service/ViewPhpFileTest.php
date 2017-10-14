<?php
namespace tests\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\Plates;
use Tuum\Respond\Service\Renderer\RawPhp;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\ViewData;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ViewPhpFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ServerRequestInterface
     */
    private $req;

    /**
     * @var ResponseInterface
     */
    private $res;

    function setup()
    {
        $_SESSION = [];
        $this->req      = ReqBuilder::createFromPath('test');
        $this->res      = new Response();
    }

    /**
     * @test
     */
    function render_with_raw_php()
    {
        $responder = Responder::forge(
            Builder::forge('test')
            ->setRenderer(new RawPhp(__DIR__ . '/views'))
        );
        $res = $responder->view($this->req, $this->res)->render('simple-text');
        $this->assertEquals('this is a simple text.', $res->getBody()->__toString());
    }

    /**
     * @test
     */
    function render_with_league_plates()
    {
        $responder = Responder::forge(
            Builder::forge('test')
                ->setRenderer(Plates::forge(__DIR__ . '/views'))
        );
        $res = $responder->view($this->req, $this->res)->render('simple-text');
        $this->assertEquals('this is a simple text.', $res->getBody()->__toString());
    }
}
