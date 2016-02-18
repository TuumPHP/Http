<?php
namespace Tuum\Respond\Service;

use Tuum\Form\DataView;
use Tuum\Respond\Interfaces\ViewDataInterface;

trait ViewerTrait
{
    /**
     * @param ViewDataInterface $viewData
     * @param DataView          $view
     * @return DataView
     */
    protected function forgeDataView($viewData = null, $view = null)
    {
        $view = $view ?: new DataView();
        $get  = function ($method) use ($viewData) {
            return ($viewData instanceof ViewDataInterface) ? $viewData->$method() : [];
        };
        $view->setData($get('getData'));
        $view->setErrors($get('getInputErrors'));
        $view->setInputs($get('getInputData'));
        $view->setMessage($get('getMessages'));

        return $view;
    }
}