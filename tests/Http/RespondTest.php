<?php
namespace tests\Http;

use tests\Tools\NoRender;
use tests\Tools\TesterTrait;
use Tuum\Respond\Builder;
use Tuum\Respond\Helper\ReqBuilder;
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
        $this->responder = Responder::forge(
            Builder::forge('test')
            ->setRenderer($view)
        )->setResponse(new Response());
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
        $response = new Response();
        $response = $this->responder->view($request, $response)->asHtml('<h1>html</h1>');
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
        $this->assertEquals('2', $response->getHeader('Content-Length')[0]);
        $this->assertEquals('dl', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_populates_ViewData_object()
    {
        $view    = $this->responder->getViewData()
            ->setData('some', 'value')
            ->setSuccess('message')
            ->setAlert('notice-msg')
            ->setError('error-msg')
            ->setInput(['more' => 'test'])
            ->setInputErrors(['more' => 'done']);

        $data = $view;

        $this->assertEquals('value', $data->getData()['some']);
        $this->assertEquals('message', $data->getMessages()[0]['message']);
        $this->assertEquals('notice-msg', $data->getMessages()[1]['message']);
        $this->assertEquals('error-msg', $data->getMessages()[2]['message']);
        $this->assertEquals('test', $data->getInput()['more']);
        $this->assertEquals('done', $data->getInputErrors()['more']);
    }
}
