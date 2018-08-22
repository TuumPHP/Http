<?php
namespace tests\Service;

use Tuum\Respond\Interfaces\PayloadInterface;
use Tuum\Respond\Responder\Payload;

require_once __DIR__ . '/../autoloader.php';

class PayloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Payload
     */
    private $payload;

    function setup()
    {
        $this->payload = new Payload();
    }

    function test()
    {
        $this->assertEquals('Tuum\Respond\Responder\Payload', get_class($this->payload));
    }

    /**
     * @test
     */
    function viewData_stores_data()
    {
        $this->payload->setInput(['inputs' => 'tested'])
            ->setInputErrors(['errors' => 'tested'])
            ->setSuccess('message: success')
            ->setAlert('message: alert')
            ->setError('message: error')
            ->setData('value', 'tested');

        $this->assertEquals(['inputs' => 'tested'], $this->payload->getInput());
        $this->assertEquals(['errors' => 'tested'], $this->payload->getInputErrors());
        $this->assertEquals(['value' => 'tested'], $this->payload->getData());
        $this->assertEquals(['message' => 'message: success', 'type' => PayloadInterface::MESSAGE_SUCCESS],
            $this->payload->getMessages()[0]);
        $this->assertEquals(['message' => 'message: alert', 'type' => PayloadInterface::MESSAGE_ALERT],
            $this->payload->getMessages()[1]);
        $this->assertEquals(['message' => 'message: error', 'type' => PayloadInterface::MESSAGE_ERROR],
            $this->payload->getMessages()[2]);
    }
}