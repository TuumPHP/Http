<?php
namespace tests\Http;

use Tuum\Http\ResponseHelper;
use Zend\Diactoros\Stream;

class ResponseHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function createResponse_creates_a_response()
    {
        $res = ResponseHelper::createResponse('testing');
        $this->assertEquals('Zend\Diactoros\Response', get_class($res));
        $this->assertEquals('testing', $res->getBody()->__toString());
        $this->assertEquals(200, $res->getStatusCode());
    }

    /**
     * @test
     */
    function createResponse_creates_from_stream()
    {
        $stream = new Stream('php://memory', 'wb+');
        $stream->write('streaming');
        $res = ResponseHelper::createResponse($stream);
        $this->assertEquals('streaming', $res->getBody()->__toString());
        $this->assertSame($stream, $res->getBody());
    }

    /**
     * @test
     */
    function createResponse_create_from_resource()
    {
        $stream = fopen('php://memory', 'wb+');
        fwrite($stream, 'resource');
        $res = ResponseHelper::createResponse($stream);
        $this->assertEquals('resource', $res->getBody()->__toString());
        $this->assertSame($stream, $res->getBody()->detach());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function createResponse_throws_exception_if_unknown_object_is_given()
    {
        $object = new \stdClass();
        ResponseHelper::createResponse($object);
    }

    /**
     * @test
     */
    function default_response_has_status_200_and_isOk()
    {
        $res = ResponseHelper::createResponse('testing');
        $this->assertTrue(ResponseHelper::isOk($res));
    }

    /**
     * @test
     */
    function getLocation_returns_location_header()
    {
        $res = ResponseHelper::createResponse('testing', 302, ['location' => 'to/test']);
        $this->assertEquals('to/test', ResponseHelper::getLocation($res));

        $res = ResponseHelper::createResponse('testing', 200, ['location' => 'more/test']);
        $this->assertEquals('more/test', ResponseHelper::getLocation($res));

        $res = ResponseHelper::createResponse('testing', 302);
        $this->assertEquals(null, ResponseHelper::getLocation($res));
    }

    /**
     * @param $status
     * @param $expected
     * 
     * @test
     * @dataProvider isDirect_Provider
     */
    function isDirect_checks_for_redirect_response($status, $expected)
    {
        $res = ResponseHelper::createResponse('testing', $status);
        $this->assertEquals($expected, ResponseHelper::isRedirect($res));
    }
    
    function isDirect_Provider()
    {
        return array(
            [301, true ],
            [302, true ],
            [303, true ],
            [307, true ],
            [200, false ],
            [304, false ],
            [300, false ]
        );
    }

    /**
     * @param $status
     * @param $expected
     *
     * @test
     * @dataProvider isInformational_Provider
     */
    function isInformational_checks_for_informational_response($method, $status, $expected)
    {
        $res = ResponseHelper::createResponse('testing', $status);
        $this->assertEquals($expected, ResponseHelper::$method($res));
    }
    
    function isInformational_Provider()
    {
        $tests = array(
            [100, 'isInformational'],
            [200, 'isSuccess'],
            [300, 'isRedirection'],
            [400, 'isClientError'],
            [500, 'isServerError'],
        );
        $bases = array(
            [00, true ],
            [01, true ],
            [20, true ],
            [99, true ],
            [100, false ],
        );
        $list = [];
        foreach($tests as $test) {
            $method   = $test[1];
            $baseCode = $test[0];
            foreach($bases as $base) {
                $code = $baseCode + $base[0];
                if ($code > 599) continue;
                $list[] = [
                    $method, 
                    $code,
                    $base[1]
                ];
            }
        }
        $list = array_merge($list, array(
            ['isError', 400, true],
            ['isError', 500, true],
            ['isError', 599, true],
            ['isError', 399, false],
        ));
        return $list;
    }

    /**
     * @test
     */
    function emit_response()
    {
        ob_start();
        $res = ResponseHelper::createResponse('emit testing', 200, ['test' => 'testing']);
        ResponseHelper::emit($res);
        $body = ob_get_clean();
        $this->assertEquals('emit testing', $body);
        $list = headers_list();
        $this->assertEquals([], $list);
    }
}
