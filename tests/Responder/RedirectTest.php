<?php
namespace tests\Responder;

use tests\Tools\TesterTrait;
use Tuum\Respond\Builder;
use Tuum\Respond\Factory;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class RedirectTest extends \PHPUnit\Framework\TestCase
{
    use TesterTrait;

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var SessionStorageInterface
     */
    private $session;

    /**
     * @var Responder
     */
    private $responder;

    function setup()
    {
        $_SESSION      = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->responder = Factory::forge();
        $this->responder->setResponse(new Response());
        $this->redirect = new Redirect();

        $request = ReqBuilder::createFromPath('test');
        $request = $this->responder->setUpRequest($request);
        $this->redirect = $this->redirect->start($request, $this->responder);
    }

    function tearDown()
    {
        unset($_SESSION);
    }

    function test0()
    {
        $this->assertEquals('Tuum\Respond\Responder\Redirect', get_class($this->redirect));
    }

    /**
     * @test
     */
    function toAbsoluteUri_sets_location_header()
    {
        $response = $this->redirect->toPath('/test/path');
        $this->assertEquals('/test/path', ResponseHelper::getLocation($response));
    }

    /**
     * @test
     */
    function toBasePath_sets_relative_to_basePath()
    {
        $request        = ReqBuilder::createFromPath('/base/path');
        $request        = ReqAttr::withBasePath($request, '/base/');
        $request = $this->responder->setUpRequest($request);
        $this->redirect = $this->redirect->start(
            $request,
            $this->responder
        );
        $response       = $this->redirect->toBasePath('path');
        $this->assertEquals('/base/path', ResponseHelper::getLocation($response));
    }

    /**
     * @test
     */
    function toReferrer_sets_referring_uri_as_location()
    {
        $request        = ReqBuilder::createFromPath('/base/path');
        $request        = ReqAttr::withReferrer($request, '/referrer/');
        $request = $this->responder->setUpRequest($request);
        $this->redirect = $this->redirect->start(
            $request,
            $this->responder
        );
        $response       = $this->redirect->toReferrer();
        $this->assertEquals('/referrer/', ResponseHelper::getLocation($response));
    }

    /**
     * @test
     */
    function withQuery_sets_query_to_uri()
    {
        $res = $this->redirect->toPath('query', ['test'=>'tested']);
        $this->assertEquals('/query?test=tested', $res->getHeaderLine('Location'));
    }
}
