<?php
namespace tests\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Builder;
use Tuum\Respond\Factory;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\RawPhp;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ViewPhpFileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ServerRequestInterface
     */
    private $req;

    /**
     * @var ResponseInterface
     */
    private $res;

    /**
     * @var Responder
     */
    private $responder;

    function setup()
    {
        $_SESSION = [];
        $this->req      = ReqBuilder::createFromPath('test');
        $this->res      = new Response();
        $this->responder = Factory::new()
            ->set(RendererInterface::class, new RawPhp(__DIR__ . '/views'))
            ->build()
            ->setResponse($this->res);
    }

    /**
     * @test
     */
    function render_with_raw_php()
    {
        $res = $this->responder->view($this->req)->render('simple-text');
        $this->assertEquals('this is a simple text.', $res->getBody()->__toString());
    }

    /**
     * @test
     */
    function render_with_league_plates()
    {
        $res = $this->responder->view($this->req)->render('simple-text');
        $this->assertEquals('this is a simple text.', $res->getBody()->__toString());
    }
}
