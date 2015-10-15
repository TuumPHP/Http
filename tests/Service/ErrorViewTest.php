<?php
namespace tests\Service;

use Tuum\Respond\Service\ErrorView;
use Tuum\Respond\Service\ViewData;
use Tuum\Respond\Service\ViewStreamInterface;
use Tuum\Respond\Service\ViewStreamTrait;

class ViewForError implements ViewStreamInterface
{
    use ViewStreamTrait;

    /**
     * renders $view_file with $data.
     *
     * @param string   $view_file
     * @param ViewData $data
     * @return ViewStreamInterface
     */
    public function withView($view_file, $data = null)
    {
        $this->view_file = $view_file;
        $this->view_data = $data;
    }

    /**
     * modifies the internal renderer's setting.
     *
     * $modifier = function($renderer) {
     *    // modify the renderer.
     * }
     *
     * @param \Closure $modifier
     * @return mixed
     */
    public function modRenderer($modifier)
    {
    }

    /**
     * @return string
     */
    protected function render()
    {
    }
    
    public function getViewFile()
    {
        return $this->view_file;
    }
    
    public function getViewData()
    {
        return $this->view_data;
    }
};

class ErrorViewException
{
    public $code;

    public function getCode()
    {
        return $this->code;
    }
}

class ErrorViewTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewForError
     */
    private $view;

    function setup()
    {
        $this->view  = new ViewForError();
    }

    /**
     * @test
     */
    function forget_sets_options()
    {
        $error = ErrorView::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $this->assertEquals('tested-default', $error->default_error);
        $this->assertEquals('tested-status',  $error->statusView['123']);
    }

    /**
     * @test
     */
    function getStream_returns_stream_with_error_code()
    {
        $error = ErrorView::forge($this->view, [
            'default' => 'tested-default',
            'status'  => [
                '123' => 'tested-status'
            ],
            'handler' => false,
        ]);
        $error->getStream('123', ['stream' => 'tested']);
        $this->assertEquals('tested-status', $this->view->getViewFile());

        $error->getStream('234', ['stream' => 'tested']);
        $this->assertEquals('tested-default', $this->view->getViewFile());
    }

    /**
     * @ test
     */
    function invoke_will_emit()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $error = ErrorView::forge($this->view, [
            'default' => 'tested-default',
        ]);
        //$error->__invoke(new ErrorViewException());
    }
}
