<?php
namespace tests\Http;

use Tuum\Respond\RequestHelper;
use Zend\Diactoros\Request;
use Zend\Diactoros\ServerRequest;

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
}
