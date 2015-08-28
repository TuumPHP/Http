<?php
namespace tests\Http;

use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder;
use Zend\Diactoros\Request;

class RequestHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function createFromPath_creates_request_with_path_and_method()
    {
        $request = RequestHelper::createFromPath('/http/test', 'post');
        $this->assertEquals('/http/test', $request->getUri()->getPath());
        $this->assertEquals('post', $request->getMethod());
    }
    
    /**
     * @test
     */
    function basePath_sets_base_path_and_path_info()
    {
        $request = RequestHelper::createFromPath('/http/base/path/test');
        $request = RequestHelper::withBasePath($request, '/http/base');
        $this->assertEquals('/http/base', RequestHelper::getBasePath($request));
        $this->assertEquals('/path/test', RequestHelper::getPathInfo($request));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function setting_wrong_basePath_throws_exception()
    {
        $request = RequestHelper::createFromPath('/http/base/path/test');
        RequestHelper::withBasePath($request, '/bad/basePath');
    }

    /**
     * @test
     */
    function setReferrer_sets_reference()
    {
        $request = RequestHelper::createFromPath('/http/base/path/test');
        $request = RequestHelper::withReferrer($request, '/refer/route');
        $this->assertEquals('/refer/route', RequestHelper::getReferrer($request));
    }

    /**
     * @test
     */
    function withResponder_and_getResponder_sets_and_return_same_object()
    {
        /** @var Responder $obj */
        $obj = $this->getMockBuilder(Responder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = RequestHelper::createFromPath('/http/base/path/test');
        $request = RequestHelper::withResponder($request, $obj);
        $obj2 = RequestHelper::getResponder($request);
        $this->assertSame($obj, $obj2);
    }

    /**
     * really bad test. do some real test on the created $request...
     *
     * @test
     */
    function exec_create_from_global()
    {
        $request = RequestHelper::createFromGlobal([]);
        $this->assertTrue($request instanceof ServerRequestInterface);
    }
}
