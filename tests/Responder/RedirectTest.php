<?php
namespace tests\Responder;

use tests\Http\TesterTrait;
use Tuum\Respond\RequestHelper;
use Tuum\Respond\Responder\Redirect;
use Tuum\Respond\ResponseHelper;
use Tuum\Respond\Service\SessionStorage;
use Tuum\Respond\Service\SessionStorageInterface;

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
        $_SESSION = [];
        $this->session  = SessionStorage::forge('tuum-app');
        $this->setPhpTestFunc($this->session);
        $this->redirect = new Redirect();
        $this->redirect = $this->redirect->withSession($this->session);
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
        $request  = RequestHelper::createFromPath('/base/path');
        $request  = RequestHelper::withBasePath($request, '/base/');
        $response = $this->redirect->withRequest($request)->toBasePath('path');
        $this->assertEquals('/base/path', ResponseHelper::getLocation($response));
    }

    /**
     * @test
     */
    function toReferrer_sets_referring_uri_as_location()
    {
        $request  = RequestHelper::createFromPath('/base/path');
        $request  = RequestHelper::withReferrer($request, '/referrer/');
        $response = $this->redirect->withRequest($request)->toReferrer();
        $this->assertEquals('/referrer/', ResponseHelper::getLocation($response));
    }
}
