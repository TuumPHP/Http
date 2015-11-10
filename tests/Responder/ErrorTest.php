<?php
namespace tests\Responder;

use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder\Error;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Responder\ViewData;
use Zend\Diactoros\Response;

class ErrorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Error
     */
    public $error;

    function setup()
    {
        $this->error = new Error(new ErrorBack());
        $this->error = $this->error->withRequest(ReqBuilder::createFromPath('test'), new Response(), SessionStorage::forge('test'), new ViewData());
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
