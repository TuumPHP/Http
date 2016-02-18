<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\Helper\ReqAttr;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Interfaces\SessionStorageInterface;
use Zend\Diactoros\Response;

class RedirectTest extends \PHPUnit_Framework_TestCase
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

    function setup()
    {
        $_SESSION      = [];
        $this->session = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->redirect = new Redirect();
        $this->redirect = $this->redirect->withRequest(
            ReqBuilder::createFromPath('test'),
            new Response(),
            $this->session
        );
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
        $response = $this->redirect->toAbsoluteUri('/test/path');
        $this->assertEquals('/test/path', ResponseHelper::getLocation($response));
    }

    /**
     * @test
     */
    function toBasePath_sets_relative_to_basePath()
    {
        $request        = ReqBuilder::createFromPath('/base/path');
        $request        = ReqAttr::withBasePath($request, '/base/');
        $this->redirect = $this->redirect->withRequest(
            $request,
            new Response(),
            $this->session
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
        $this->redirect = $this->redirect->withRequest(
            $request,
            new Response(),
            $this->session
        );
        $response       = $this->redirect->toReferrer();
        $this->assertEquals('/referrer/', ResponseHelper::getLocation($response));
    }
}
