<?php
/** @var Renderer $this */
/** @var ViewHelper $view */

use Tuum\Respond\Service\ViewHelper;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');

?>

<style type="text/css">
    li {
        margin-top: .5em;
    }
    li>a {
        font-weight:bold;
    }
</style>

<?= $view->render('layouts/broadcast'); ?>

<?= $view->message(); ?>

<div class="col-md-6">

    <h3>Documents</h3>

    <p>more about Tuum/Respond</p>

    <ul>
        <li><h4><a href="/docs/" >Document Top</a></h4></li>
        <li><h4>Getting Started</h4>
            <ul>
                <li><a href="/docs/introduction" >Introduction</a></li>
                <li><a href="/docs/samples" >Sample Codes</a></li>
                <li><a href="/docs/structure" >Directory Structure</a></li>
            </ul></li>
        <li>    <h4>Details</h4>
            <ul>
                <li><a href="/docs/templates" >Template and Helpers</a></li>
                <li><a href="/docs/responders" >Responders</a></li>
                <li><a href="/docs/services" >Services</a></li>
            </ul>
        </li>
    </ul>

</div>

<div class="col-md-6">

    <h3>samples</h3>

    <ul>
        <li><a href="jump">form and jump</a><br/>
            form and post-redirect-get pattern sample</li>

        <li><a href="upload">file upload</a><br/>
            file upload sample using presentation object. </li>
    </ul>

    <h3>error samples</h3>

    <ul>
        <li><a href="not-such-file">not found</a><br/>
            case for 404 not-found error.</li>
        <li><a href="throw">catch exception</a><br/>
            case for catching exception. </li>
    </ul>

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
