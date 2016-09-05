<?php
use League\Plates\Template\Template;
use Tuum\Form\Components\BreadCrumb;
use Tuum\Form\Components\NavBar;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var $view ViewHelper */

$path = basename($view->request()->getUri()->getPath());
$nav  = new NavBar('documents', $path);
if ($path === 'docs') {
    $bread = new BreadCrumb('Documents');
} else {
    $bread = new BreadCrumb(ucwords($path));
    $bread->add('Documents', '/docs/');
}
$this->layout('layouts/layout', [
    'nav' => $nav,
    'bread' => $bread,
]);

?>

<?php $this->start('contents'); ?>

<div class="col-md-3">
    <br/>
    <h4>Documents</h4>
    <ul class="nav nav-pills nav-stacked">
        <li role="presentation" class="<?= $nav->m('documents', 'docs'); ?>"><a href="/docs/" >Document Top</a></li>
        <li role="separator" class="divider"><hr></li>
        <li role="presentation" class="disabled"><a href="#" >Getting Started</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'samples'); ?>"><a href="/docs/samples" >Sample Codes</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'structure'); ?>"><a href="/docs/structure" >Directory Structure</a></li>
        <li role="separator" class="divider"><hr></li>
        <li role="presentation" class="disabled"><a href="#" >Details</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'templates'); ?>"><a href="/docs/templates" >Template and Helpers</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'responders'); ?>"><a href="/docs/responders" >Responders</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'services'); ?>"><a href="/docs/services" >Services</a></li>
        <li role="presentation" class="<?= $nav->m('documents', 'breadcrumb'); ?>"><a href="/docs/breadcrumb" >BreadCrumb</a></li>
    </ul>
</div>

<div class="col-md-9">
    <?= isset($contents) ? $contents: ''; ?>
</div>

<?php $this->stop(); ?>
