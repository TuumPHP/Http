<?php
namespace Tuum\Respond\Service;

use Tuum\Form\DataView;
use Tuum\Respond\Responder\ViewData;

trait ViewerTrait
{
    /**
     * @param ViewData $data
     * @return DataView
     */
    protected function forgeDataView(ViewData $data)
    {
        $view = new DataView();
        $view->setData($data->getData());
        $view->setErrors($data->getInputErrors());
        $view->setInputs($data->getInputData());
        $view->setMessage($data->getMessages());

        return $view;
    }
}