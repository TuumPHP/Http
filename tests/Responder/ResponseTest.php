<?php
namespace tests\Responder;

use Http\Factory\Diactoros\ResponseFactory;
use Http\Factory\Diactoros\StreamFactory;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\Renderer\RawPhp;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    private function buildResponse()
    {
        $builder = Builder::forge('test');
        $responder = Responder::forge($builder)
            ->setResponse(new Response());
        return $responder;
    }
    
    private function buildFactory()
    {
        $builder = Builder::forge('test')
            ->setResponseFactory(new ResponseFactory())
            ->setStreamFactory(new StreamFactory());
        $responder = Responder::forge($builder);
        return $responder;
    }
    
    public function responderProvider()
    {
        return [
            'with response' => [$this->buildResponse()],
            'with factory' => [$this->buildFactory()],
        ];
    }

    /**
     * @dataProvider responderProvider
     * @param Responder $responder
     */
    public function testResponderMakesResponseObject($responder)
    {
        $code = 200;
        $text = 'test-response';
        $response = $responder->makeResponse($code, $text, ['x-test' => 'tested']);

        $this->assertEquals($code, $response->getStatusCode());

        $stream = $response->getBody();
        $stream->rewind();
        $this->assertEquals($text, $stream->getContents());

        $this->assertEquals('tested', $response->getHeaderLine('x-test'));
    }
}
