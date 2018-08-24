<?php
namespace tests\Responder;

use tests\Tools\TesterTrait;
use tests\Tools\NoRender;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Error;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ErrorTest extends \PHPUnit\Framework\TestCase
{
    use TesterTrait;

    /**
     * @var Error
     */
    public $error;

    function setup()
    {
        $_SESSION      = [];
        $responder = Responder::forge(
            Builder::forge('test')
            ->setRenderer(new NoRender())
        )->setResponse(new Response());
        $request = ReqBuilder::createFromPath('test');
        $request = $responder->setPayload($request);
        $this->error = $responder->error($request);
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder\Error', get_class($this->error));
    }

    /**
     * @test
     */
    function asView_returns_code()
    {
        $response = $this->error->asView(567);
        $this->assertEquals(567, $response->getStatusCode());
    }

    /**
     * @test
     */
    function notFound_returns_404()
    {
        $this->assertEquals(404, $this->error->notFound()->getStatusCode());
    }

    /**
     * @test
     */
    function forbidden_returns_403()
    {
        $this->assertEquals(403, $this->error->forbidden()->getStatusCode());
    }

    /**
     * @test
     */
    function unauthorized_returns_401()
    {
        $this->assertEquals(401, $this->error->unauthorized()->getStatusCode());
    }

    /**
     * @test
     */
    function asJson_returns_code()
    {
        $response = $this->error->asJson(456, ['more' => 'test']);
        $this->assertEquals(456, $response->getStatusCode());
        $this->assertEquals('{"more":"test"}', (string)$response->getBody());
    }
}
