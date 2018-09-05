<?php
namespace tests\Http;

use tests\Tools\NoRender;
use tests\Tools\TesterTrait;
use Tuum\Respond\Builder;
use Tuum\Respond\Factory;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Interfaces\RendererInterface;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\SessionStorage;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class RespondTest extends \PHPUnit\Framework\TestCase
{
    use TesterTrait;

    /**
     * @var SessionStorage
     */
    public $session_factory;

    /**
     * @var Responder
     */
    public $responder;

    function setup()
    {
        $_SESSION              = [];
        $this->session_factory = SessionStorage::forge('testing');
        $this->setPhpTestFunc($this->session_factory);

        $view            = new NoRender();
        $this->responder = Factory::new()
            ->set(RendererInterface::class, $view)
            ->build()
            ->setResponse(new Response());
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    /**
     * @test
     */
    function Respond_class_invokes_responder_object()
    {
        $request = ReqBuilder::createFromPath('/path/test');
        $request = $this->responder->setPayload($request);
        Respond::setResponder($this->responder);

        $response = Respond::view($request)->asText('test Respond');
        $this->assertEquals('text/plain', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('test Respond', $response->getBody()->__toString());

        $response = Respond::error($request)->notFound();
        $this->assertEquals(404, $response->getStatusCode());

        $response = Respond::redirect($request)->toPath('/tested');
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/tested', $response->getHeader('Location')[0]);
    }

    /**
     * @test
     */
    function Respond_asText_creates_text_response()
    {
        $request  = ReqBuilder::createFromPath('/path/test');
        $response = $this->responder->view($request)->asText('text response');
        $this->assertEquals('text/plain', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('text response', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asHtml_creates_html_response()
    {
        $request  = ReqBuilder::createFromPath('/path/test');
        $response = $this->responder->view($request)->asHtml('<h1>html</h1>');
        $this->assertEquals('text/html', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('<h1>html</h1>', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asJson_creates_json_response()
    {
        $request  = ReqBuilder::createFromPath('/path/test');
        $response = $this->responder->view($request)->asJson(['jason' => 'type']);
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('{"jason":"type"}', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asDownload_creates_download_response()
    {
        $request  = ReqBuilder::createFromPath('/path/test');
        $response = $this->responder->view($request)->asDownload('dl', 'dl-name');
        $this->assertEquals('application/octet-stream', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('attachment; filename="dl-name"', $response->getHeader('Content-Disposition')[0]);
        $this->assertEquals('dl', $response->getBody()->__toString());
    }
}
