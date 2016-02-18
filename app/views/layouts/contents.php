<?php
use Tuum\Form\DataView;

/** @var $view DataView */

$this->setLayout('layouts/layout');
?>
<?= $view->data->raw('contents'); ?>