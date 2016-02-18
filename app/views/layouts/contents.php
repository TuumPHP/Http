<?php
use Tuum\Form\DataView;

/** @var $view DataView */

$this->setLayout('layouts/layout');
?>
<?= isset($contents) ? $contents: ''; ?>