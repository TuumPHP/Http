<?php
namespace tests\Service;

use Tuum\Respond\Interfaces\ViewDataInterface;
use Tuum\Respond\Service\ViewData;

require_once __DIR__ . '/../autoloader.php';

class ViewDataTest extends \PHPUnit\Framework\TestCase
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
        $this->view->setInput(['inputs' => 'tested'])
            ->setInputErrors(['errors' => 'tested'])
            ->setSuccess('message: success')
            ->setAlert('message: alert')
            ->setError('message: error')
            ->setData('value', 'tested');

        $this->assertEquals(['inputs' => 'tested'], $this->view->getInput());
        $this->assertEquals(['errors' => 'tested'], $this->view->getInputErrors());
        $this->assertEquals(['value' => 'tested'], $this->view->getData());
        $this->assertEquals(['message' => 'message: success', 'type' => ViewDataInterface::MESSAGE_SUCCESS],
            $this->view->getMessages()[0]);
        $this->assertEquals(['message' => 'message: alert', 'type' => ViewDataInterface::MESSAGE_ALERT],
            $this->view->getMessages()[1]);
        $this->assertEquals(['message' => 'message: error', 'type' => ViewDataInterface::MESSAGE_ERROR],
            $this->view->getMessages()[2]);
    }
}