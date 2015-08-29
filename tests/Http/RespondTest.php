<?php
namespace tests\Http;

use Tuum\Respond\RequestHelper;
use Tuum\Respond\Respond;
use Tuum\Respond\Responder;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewStream;

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
        $this->session_factory = SessionStorage::forge([]);
        $this->setPhpTestFunc($this->session_factory);

        $view = ViewStream::forge('');
        $this->responder = Responder::build(
            $view,
            new ErrorView($view)
        );
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
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, SessionStorage::forge('testing'));
        $request  = RequestHelper::withResponder($request, $this->responder);

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
        $request  = RequestHelper::createFromPath('/path/test');
        $response = $this->responder->view($request)->asText('text response');
        $this->assertEquals('text/plain', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('text response', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asHtml_creates_html_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
        $response = ResponseHelper::createResponse('');
        $response = $this->responder->view($request, $response)->asHtml('<h1>html</h1>');
        $this->assertEquals('text/html', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('<h1>html</h1>', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asJson_creates_json_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
        $response = $this->responder->view($request)->asJson(['jason'=>'type']);
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('{"jason":"type"}', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asDownload_creates_download_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
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
        $session  = $this->session_factory->withStorage('tuum-app');
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session);
        $respond  = $this->responder->view($request)
            ->with('some', 'value')
            ->withMessage('message')
            ->withAlertMsg('notice-msg')
            ->withErrorMsg('error-msg')
            ->withInputData(['more' => 'test'])
            ->withInputErrors(['more' => 'done']);

        $refObj  = new \ReflectionObject($respond);
        $refData = $refObj->getProperty('data');
        $refData->setAccessible(true);
        /** @var ViewData $data */
        $data    = $refData->getValue($respond);

        $this->assertEquals('value', $data->get(ViewData::DATA)['some']);
        $this->assertEquals('message', $data->get(ViewData::MESSAGE)[0]['message']);
        $this->assertEquals('notice-msg', $data->get(ViewData::MESSAGE)[1]['message']);
        $this->assertEquals('error-msg', $data->get(ViewData::MESSAGE)[2]['message']);
        $this->assertEquals('test', $data->get(ViewData::INPUTS)['more']);
        $this->assertEquals('done', $data->get(ViewData::ERRORS)['more']);
    }

    /**
     * @test
     */
    function Respond_passes_data_by_withFlashData()
    {
        $session  = $this->session_factory->withStorage('tuum-app');
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session);
        $this->responder->view($request)->withFlashData('with-flash', 'value1');

        /*
         * next request.
         * move the flash, i.e. next request.
         */
        $session->commit();
        $this->moveFlash($this->session_factory);

        $this->assertEquals('value1', RequestHelper::getFlash($request, 'with-flash'));
    }
}
