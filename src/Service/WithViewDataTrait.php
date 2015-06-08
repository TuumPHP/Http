<?php
namespace Tuum\Http\Service;

use Tuum\Application\Application;

trait WithViewDataTrait
{
    /**
     * @var ViewData
     */
    private $data;

    /**
     * @param array       $options
     * @param Application $app
     */
    protected function setViewData(array $options, $app)
    {
        if (isset($options[ViewData::class])) {
            $this->data = $options[ViewData::class];
        } elseif ($app && $app->exists(ViewData::class)) {
            $this->data = $app->get(ViewData::class);
        } else {
            $this->data = new ViewData();
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