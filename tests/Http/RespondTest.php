<?php
namespace tests\Http;

use Aura\Session\SessionFactory;
use Tuum\Http\RequestHelper;
use Tuum\Http\Responder\View;
use Tuum\Http\ResponseHelper;
use Tuum\Http\Service\ViewData;

class RespondTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SessionFactory
     */
    public $session_factory;

    function setup()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION = [];
        $this->session_factory = new SessionFactory();
    }

    /**
     * @test
     */
    function Respond_asText_creates_text_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
        $response = View::forge($request)->asText('text response');
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
        $response = View::forge($request, $response)->asHtml('<h1>html</h1>');
        $this->assertEquals('text/html', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('<h1>html</h1>', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asJson_creates_json_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
        $response = View::forge($request)->asJson(['jason'=>'type']);
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
        $this->assertEquals('{"jason":"type"}', $response->getBody()->__toString());
    }

    /**
     * @test
     */
    function Respond_asDownload_creates_download_response()
    {
        $request  = RequestHelper::createFromPath('/path/test');
        $response = View::forge($request)->asDownload('dl', 'dl-name');
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
        $session  = $this->session_factory->newInstance([]);
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));
        $respond  = View::forge($request)
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
        $session  = $this->session_factory->newInstance([]);
        $request  = RequestHelper::createFromPath('/path/test');
        $request  = RequestHelper::withSessionMgr($request, $session->getSegment('tuum-app'));
        View::forge($request)->withFlashData('with-flash', 'value1');

        /*
         * next request.
         * move the flash, i.e. next request.
         */
        $session->commit();
        $move = new \ReflectionMethod($session, 'moveFlash');
        $move->setAccessible(true);
        $move->invoke($session);

        $this->assertEquals('value1', RequestHelper::getFlash($request, 'with-flash'));
    }
}
