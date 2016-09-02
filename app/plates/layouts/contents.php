<?php
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var $view ViewHelper */

$_view_data = [];
if (isset($nav)) {
    $_view_data['nav'] = $nav;
}
$this->layout('layouts/layout', $_view_data);

?>

<?php $this->start('contents'); ?>

<?= isset($contents) ? $contents: ''; ?>

<?php $this->stop(); ?>
