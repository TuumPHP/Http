<?php
namespace tests\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuum\Respond\Helper\ReqBuilder;
use Tuum\Respond\Responder\View;
use Tuum\Respond\Service\RenderError;
use Tuum\Respond\Responder\ViewData;
use Tuum\Respond\Service\ViewerTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;

class ViewForError
{
    use ViewerTrait;

    public $view_file;

    public $view_data;
    
    public $request;
    
    public $response;

    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @return $this
     */
    public function withRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        return $this;
    }

    /**
     * @param string                 $viewFile
     * @param mixed|ViewData         $viewData
     * @return $this
     */
    public function render( $viewFile, $viewData)
    {
        $this->view_file = $viewFile;
        $this->view_data = $viewData;
        return $this;
    }



    public function getViewFile()
    {
        return $this->view_file;
    }

    public function getViewData()
    {
        return $this->view_data;
    }
}

;

class ErrorViewException extends \Exception
{
}

class ErrorViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View|ViewForError
     */
    private $view;

    /**
     * @var ServerRequestInterface
     */
    public $req;

    /**
     * @var ResponseInterface
     */
    public $res;

    /**
     * @var ViewData
     */
    public $viewData;

    function setup()
    {
        $this->view     = new ViewForError();
        $this->req      = ReqBuilder::createFromPath('test');
        $this->res      = new Response();
        $this->viewData = new ViewData();
    }

    /**
     * @test
     */
    function forget_sets_options()
    {
        $error = RenderError::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $this->assertEquals('tested-default', $error->default_error);
        $this->assertEquals('tested-status', $error->statusView['123']);
    }

    /**
     * @test
     */
    function getStream_returns_stream_with_error_code()
    {
        $error = RenderError::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $error->__invoke($this->req, $this->res, 123, $this->viewData);
        $this->assertEquals('tested-status', $this->view->getViewFile());

        $error->__invoke($this->req, $this->res, 234, $this->viewData);
        $this->assertEquals('tested-default', $this->view->getViewFile());
    }
}
