<?php
namespace tests\Http;

use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\TuumViewer;
use Tuum\Respond\Service\ViewData;
use Zend\Diactoros\Response;

class RespondTest extends \PHPUnit_Framework_TestCase
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
        $_SESSION = [];
        $this->session_factory = SessionStorage::forge('testing');
        $this->setPhpTestFunc($this->session_factory);

        $view = TuumViewer::forge('');
        $this->responder = Responder::build(
            $view,
            new ErrorView($view)
        )->withResponse(new Response())
        ->withSession($this->session_factory);
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
        $request  = ReqBuilder::createFromPath('/path/test');
        $request  = Respond::withResponder($request, $this->responder);

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
        $response = $this->responder->view($request)->asJson(['jason'=>'type']);
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
        $request  = ReqBuilder::createFromPath('/path/test');
        $respond  = $this->responder->view($request)
            ->withData('some', 'value')
            ->withSuccess('message')
            ->withAlert('notice-msg')
            ->withError('error-msg')
            ->withInputData(['more' => 'test'])
            ->withInputErrors(['more' => 'done']);

        $refObj  = new \ReflectionObject($respond);
        $refData = $refObj->getProperty('data');
        $refData->setAccessible(true);
        /** @var ViewData $data */
        $data    = $refData->getValue($respond);

        $this->assertEquals('value', $data->getData()['some']);
        $this->assertEquals('message', $data->getMessages()[0]['message']);
        $this->assertEquals('notice-msg', $data->getMessages()[1]['message']);
        $this->assertEquals('error-msg', $data->getMessages()[2]['message']);
        $this->assertEquals('test', $data->getInputData()['more']);
        $this->assertEquals('done', $data->getInputErrors()['more']);
    }

    /**
     * @test
     */
    function Respond_passes_data_by_withFlashData()
    {
        $request  = ReqBuilder::createFromPath('/path/test');
        $this->responder->view($request)->withFlashData('with-flash', 'value1');

        /*
         * next request.
         * move the flash, i.e. next request.
         */
        $this->session_factory->commit();
        $this->moveFlash($this->session_factory);

        $this->assertEquals('value1', $this->responder->session()->getFlash('with-flash'));
    }

    /**
     * @test
     */
    function with()
    {
        $request = ReqBuilder::createFromPath('/path/test');
        $request = Respond::withResponder($request, $this->responder);
        $request = Respond::withViewData($request, function(ViewData $view) {
            $view->data('test', 'tested');
            return $view;
        });
        $view   = Respond::view($request);
        $refObj = new \ReflectionObject($view);
        $refPro = $refObj->getProperty('data');
        $refPro->setAccessible(true);
        /** @var ViewData $data */
        $data   = $refPro->getValue($view);
        $this->assertEquals('tested', $data->getData()['test']);
    }
}
