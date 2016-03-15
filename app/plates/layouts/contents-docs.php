<?php
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var $view ViewHelper */

$this->layout('layouts/layout', ['view' => $view]);

?>

<?php $this->start('contents'); ?>

<div class="col-md-3">
    <h3>Documents</h3>
    <ul>
        <li><a href="/docs/introduction" >Introduction</a></li>
        <li><a href="/docs/samples" >Sample Codes</a></li>
        <li><a href="/docs/structure" >Directory Structure</a></li>
        <li><a href="/docs/templates" >Template and Helpers</a></li>
    </ul>
</div>

<div class="col-md-9">
    <?= isset($contents) ? $contents: ''; ?>
</div>

<?php $this->stop(); ?>
