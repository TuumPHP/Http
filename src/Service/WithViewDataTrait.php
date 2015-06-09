<?php
namespace Tuum\Http\Service;

use Interop\Container\ContainerInterface;

trait WithViewDataTrait
{
    /**
     * @var ViewData
     */
    protected $data;

    /**
     * @param array              $options
     * @param ContainerInterface $app
     */
    protected function setViewData(array $options, $app)
    {
        if (isset($options[ViewData::class])) {
            $this->data = $options[ViewData::class];
        } elseif ($app && $app->has(ViewData::class)) {
            $this->data = $app->get(ViewData::class);
        } else {
            $this->data = new ViewData();
        }
    }

    /**
     * @param string|array $key
     * @param mixed|null   $value
     */
    protected function withData($key, $value=null)
    {
        if (is_array($key)) {
            $this->data->setRawData($key);
        } elseif (is_string($key)) {
            $this->data->set($key, $value);
        }
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInputData(array $input)
    {
        $this->data->inputData($input);
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withInputErrors(array $errors)
    {
        $this->data->inputErrors($errors);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage($message)
    {
        $this->data->success($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withAlertMsg($message)
    {
        $this->data->alert($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withErrorMsg($message)
    {
        $this->data->error($message);
        return $this;
    }
}