<?php
namespace tests\Http;

use tests\Tools\NoRender;
use tests\Tools\TesterTrait;
use Tuum\Respond\Builder;
use Tuum\Respond\Responder;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Helper\ResponseHelper;
use Tuum\Respond\Service\SessionStorage;
use Zend\Diactoros\Response;

require_once __DIR__ . '/../autoloader.php';

class RedirectAndRespondTest extends \PHPUnit_Framework_TestCase
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
        $this->session_factory = SessionStorage::forge('test');
        $this->setPhpTestFunc($this->session_factory);

        $this->responder = $this->createResponder();
    }

    /**
     * @return Responder
     */
    private function createResponder()
    {
        $view            = new NoRender();
        return Responder::forge(
            Builder::forge('test')
                ->setRenderer($view)
        )->withResponse(new Response());
    }

    /**
     * create a redirect response with various data (error message, input data, input errors).
     *
     * the subsequent request will create a respond with the data set in the previous redirect response.
     */
    function test()
    {
        /*
         * a redirect response with various data.
         */
        $request  = ReqBuilder::createFromPath('/path/test');
        $this->responder->session()
            ->setFlash('with', 'val1');

        $response     = $this->responder->redirect($request)
            ->setData('more', 'with')
            ->setSuccess('message')
            ->setAlert('notice-msg')
            ->setError('error-msg')
            ->setInput(['more' => 'test'])
            ->setInputErrors(['more' => 'done'])
            ->toPath('/more/test');
        $this->assertEquals('/more/test', ResponseHelper::getLocation($response));

        /*
         * next request. 
         * move the flash, i.e. next request.
         */
        $this->session_factory->commit();
        $stored   = serialize($_SESSION);
        $_SESSION = unserialize($stored);

        $this->moveFlash($this->session_factory);

        /*
         * next request with the data from the previous redirection. 
         */
        $responder = $this->createResponder();

        $data = $responder->getViewData();

        $this->assertEquals('val1', $responder->session()->getFlash('with'));
        $this->assertEquals('with', $data->getData()['more']);
        $this->assertEquals('message', $data->getMessages()[0]['message']);
        $this->assertEquals('notice-msg', $data->getMessages()[1]['message']);
        $this->assertEquals('error-msg', $data->getMessages()[2]['message']);
        $this->assertEquals('test', $data->getInput()['more']);
        $this->assertEquals('done', $data->getInputErrors()['more']);
    }
}
