<?php
/** @var Template $this */
/** @var DataView $view */
use League\Plates\Template\Template;
use Tuum\Form\DataView;

$this->layout('layouts/layout');

?>

<?php $this->start('contents'); ?>

<h1>Unauthorized Access</h1>

<p>File or resource you have requested for was not authorized. </p>
<p><a href="/">start from the beginning!</a></p>

<?php $this->stop(); ?>