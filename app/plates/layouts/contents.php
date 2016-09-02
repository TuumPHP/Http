<?php
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var $view ViewHelper */

$this->layout('layouts/layout', $this->data);

?>

<?php $this->start('contents'); ?>

<?= isset($contents) ? $contents: ''; ?>

<?php $this->stop(); ?>
