<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

$this->layout('layouts/layout');

?>

<?php $this->start('contents'); ?>

<?= $view->render('layouts/broadcast'); ?>

<?= $view->message(); ?>

<div class="col-sm-6 col-md-4">

    <h3>Documents</h3>

    <p>more about Tuum/Respond</p>

    <ul>
        <li><a href="/docs/introduction" >Introduction</a></li>
        <li><a href="/docs/responders" >Responders</a></li>
        <li><a href="/docs/template" >Template and Renderer</a></li>
        <li><a href="/docs/controller" >Controller Helper</a></li>
    </ul>

</div>

<div class="col-sm-6 col-md-4">

    <h3>samples</h3>

    <ul>
        <li><a href="jump">form and jump</a><br/>
            form and post-redirect-get pattern sample</li>

        <li><a href="upload">file upload</a><br/>
            file upload sample using presentation object. </li>

    </ul>

</div>

<div class="col-sm-6 col-md-4">

    <h3>error samples</h3>

    <ul>
        <li><a href="not-such-file">not found</a><br/>
            case for 404 not-found error.</li>
        <li><a href="throw">catch exception</a><br/>
            case for catching exception. </li>
        <li><form method="post" action="forbidden" ><input type="submit" value="case for forbidden errors" class="btn btn-default btn-xs" /></form></li>
    </ul>

</div>

<div class="col-sm-6 col-md-4">

    <h3>more samples</h3>

    <ul>
        <li><a href="content">content</a><br/>
            a simple html content sample. </li>
        <li><a href="forms">form samples</a><br/>
            more form generation using Tuum/Form helpers.</li>
        <li><a href="objGraph">object graph</a><br/>
            view object graph of $responder using koriym/printo</li>
    </ul>

</div>

<?php $this->stop(); ?>