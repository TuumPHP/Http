<?php
/** @var Template $this */
/** @var DataView $view */
use League\Plates\Template\Template;
use Tuum\Form\DataView;

$this->layout('layouts/layout');

?>

<?php $this->start('contents'); ?>

<h1>File Not Found</h1>

<p>File or resource you have requested for was not found. </p>
<p><a href="/">start from the beginning!</a></p>

<?php $this->stop(); ?>