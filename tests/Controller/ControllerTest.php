<?php
namespace tests\Controller;

use tests\Controller\MockControllers\ByMethodController;
use tests\Controller\MockControllers\ByRouteController;
use Tuum\Respond\Helper\ReqBuilder;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    function ByMethod_DispatchesAppropriateMethod()
    {
        $req = ReqBuilder::createFromPath('test', 'POST');
        $res = new Response();
        $controller = new ByMethodController();
        $returned = $controller->test($req, $res);
        $returned->getBody()->rewind();
        $this->assertEquals('test:post', $returned->getBody()->getContents());
    }

    /**
     * @test
     */
    function ByMethod_onGetWithParameterIsFilled()
    {
        $req = ReqBuilder::createFromPath('test', 'GET')->withQueryParams(['test' => 'filled']);
        $res = new Response();
        $controller = new ByMethodController();
        $returned = $controller->test($req, $res);
        $returned->getBody()->rewind();
        $this->assertEquals('test:filled', $returned->getBody()->getContents());
    }

    /**
     * @test
     */
    function ByMethod_optionReturnsAllow()
    {
        $req = ReqBuilder::createFromPath('test', 'OPTIONS');
        $res = new Response();
        $controller = new ByMethodController();
        $returned = $controller->test($req, $res);
        $allowed  = $returned->getHeaderLine('allow');
        $this->assertEquals('GET,OPTIONS,POST', $allowed);
    }

    /**
     * @test
     */
    function ByMethod_nonSupportedMethodReturnsNull()
    {
        $req = ReqBuilder::createFromPath('test', 'not-supported');
        $res = new Response();
        $controller = new ByMethodController();
        $returned = $controller->test($req, $res);
        $this->assertEquals(null, $returned);
    }

    /**
     * @test
     */
    function ByRoute_DispatchesMethods()
    {
        $req = ReqBuilder::createFromPath('/', 'GET');
        $res = new Response();
        $controller = new ByRouteController();
        $returned = $controller->test($req, $res);
        $returned->getBody()->rewind();
        $this->assertEquals('route:get', $returned->getBody()->getContents());

    }

    /**
     * @test
     */
    function ByRoute_DispatchesWithArgument()
    {
        $req = ReqBuilder::createFromPath('/my/tests', 'GET');
        $res = new Response();
        $controller = new ByRouteController();
        $returned = $controller->test($req, $res);
        $returned->getBody()->rewind();
        $this->assertEquals('route:tests', $returned->getBody()->getContents());
    }
    /**
     * 
     * @test
     */
    function ByRoute_optionReturnsAllow()
    {
        $req = ReqBuilder::createFromPath('/', 'OPTIONS');
        $res = new Response();
        $controller = new ByRouteController();
        $returned = $controller->test($req, $res);
        $allowed  = $returned->getHeaderLine('allow');
        $this->assertEquals('GET,HEAD,OPTIONS', $allowed);
    }

    /**
     * @test
     */
    function ByRoute_nonSupportedMethodReturnsNull()
    {
        $req = ReqBuilder::createFromPath('/', 'POST');
        $res = new Response();
        $controller = new ByRouteController();
        $returned = $controller->test($req, $res);
        $this->assertEquals(null, $returned);
    }

}