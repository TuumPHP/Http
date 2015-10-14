<?php
namespace tests\Service;

use Tuum\Respond\Service\ViewData;

class ViewDataTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ViewData
     */
    private $view;
    
    function setup()
    {
        $this->view = new ViewData();
    }
    
    function test()
    {
        $this->assertEquals('Tuum\Respond\Service\ViewData', get_class($this->view));
    }

    /**
     * @test
     */
    function viewData_stores_data()
    {
        $this->view->inputData(['inputs' => 'tested']);
        $this->view->inputErrors(['errors' => 'tested']);
        $this->view->success('message: success');
        $this->view->alert('message: alert');
        $this->view->error('message: error');
        $this->view->dataValue('value', 'tested');  
        
        $this->assertEquals(['inputs' => 'tested'], $this->view->get(ViewData::INPUTS));
        $this->assertEquals(['errors' => 'tested'], $this->view->get(ViewData::ERRORS));
        $this->assertEquals(['value' => 'tested'], $this->view->get(ViewData::DATA));
        $this->assertEquals(['message' => 'message: success', 'type' => ViewData::MESSAGE_SUCCESS], $this->view->get(ViewData::MESSAGE)[0]);
        $this->assertEquals(['message' => 'message: alert', 'type' => ViewData::MESSAGE_ALERT], $this->view->get(ViewData::MESSAGE)[1]);
        $this->assertEquals(['message' => 'message: error', 'type' => ViewData::MESSAGE_ERROR], $this->view->get(ViewData::MESSAGE)[2]);
    }
}