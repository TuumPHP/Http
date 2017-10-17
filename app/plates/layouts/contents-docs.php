<?php
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var $view ViewHelper */

$path = basename($view->request()->getUri()->getPath());
$this->layout('layouts/layout');

?>

<?php $this->start('contents'); ?>

<div class="col-sm-3">
    <br/>
    <h4>Documents</h4>
    <ul class="nav nav-pills nav-stacked">
        <li role="presentation"><a href="/docs/readme" >Table of Contents</a></li>
        <li role="presentation"><a href="/docs/introduction" >Introduction</a></li>
        <li role="presentation"><a href="/docs/responders" >Responders</a></li>
        <li role="presentation"><a href="/docs/template" >Template and Renderer</a></li>
        <li role="presentation"><a href="/docs/controller" >Controller Helper</a></li>
    </ul>
</div>

<div class="col-sm-9">
    <?= isset($contents) ? $contents: ''; ?>
</div>

<?php $this->stop(); ?>
