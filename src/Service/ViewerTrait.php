<?php
namespace Tuum\Respond\Service;

use Tuum\Form\DataView;
use Tuum\Respond\Interfaces\ViewDataInterface;

trait ViewerTrait
{
    /**
     * @param ViewDataInterface $data
     * @param DataView $view
     * @return DataView
     */
    protected function forgeDataView($data = null, $view = null)
    {
        $view = $view ?: new DataView();
        $get = function($method) use($data) {
            return ($data instanceof ViewDataInterface )? $data->$method(): [];
        };
        $view->setData($get('getData'));
        $view->setErrors($get('getInputErrors'));
        $view->setInputs($get('getInputData'));
        $view->setMessage($get('getMessages'));

        return $view;
    }
}