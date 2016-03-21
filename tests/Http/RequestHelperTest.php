<?php
namespace tests\Http;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Zend\Diactoros\Request;

class RequestHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function createFromPath_creates_request_with_path_and_method()
    {
        $request = ReqBuilder::createFromPath('/http/test', 'post');
        $this->assertEquals('/http/test', $request->getUri()->getPath());
        $this->assertEquals('post', $request->getMethod());
    }

    /**
     * @test
     */
    function basePath_sets_base_path_and_path_info()
    {
        $request = ReqBuilder::createFromPath('/http/base/path/test');
        $request = ReqAttr::withBasePath($request, '/http/base');
        $this->assertEquals('/http/base', ReqAttr::getBasePath($request));
        $this->assertEquals('/path/test', ReqAttr::getPathInfo($request));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function setting_wrong_basePath_throws_exception()
    {
        $request = ReqBuilder::createFromPath('/http/base/path/test');
        ReqAttr::withBasePath($request, '/bad/basePath');
    }

    /**
     * @test
     */
    function setReferrer_sets_reference()
    {
        $request = ReqBuilder::createFromPath('/http/base/path/test');
        $request = ReqAttr::withReferrer($request, '/refer/route');
        $this->assertEquals('/refer/route', ReqAttr::getReferrer($request));
    }

    /**
     * @test
     */
    function withResponder_and_getResponder_sets_and_return_same_object()
    {
        /** @var Responder $obj */
        $obj     = $this->getMockBuilder(Responder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = ReqBuilder::createFromPath('/http/base/path/test');
        $request = Respond::withResponder($request, $obj);
        $obj2    = Respond::getResponder($request);
        $this->assertSame($obj, $obj2);
    }

    /**
     * really bad test. do some real test on the created $request...
     *
     * @test
     */
    function exec_create_from_global()
    {
        $request = ReqBuilder::createFromGlobal([]);
        $this->assertTrue($request instanceof ServerRequestInterface);
    }

    /**
     * @test
     */
    function with_and_get_method()
    {
        $request = ReqBuilder::createFromPath('test', 'POST', [], ['_test_method' => 'tested']);
        $request = ReqAttr::withMethod($request, '_test_method');
        $this->assertEquals('tested', ReqAttr::getMethod($request));
    }
}
