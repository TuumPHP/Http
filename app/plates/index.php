<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

$this->layout('layouts/layout');

?>

<?php $this->start('contents'); ?>

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

    <h3>samples</h3>
    <ul>
        <li><a href="jump">form and jump</a><br/>
            form and post-redirect-get pattern sample</li>

        <li><a href="upload">file upload</a><br/>
            file upload sample using presentation object. </li>
    </ul>

    <h3>content</h3>
    <ul>
        <li><a href="content">content</a></li>
    </ul>

</div>

<div class="col-md-6">

    <h3>error samples</h3>

    <ul>
        <li><a href="not-such-file">not found</a><br/>
            case for 404 not-found error.</li>
        <li><a href="throw">catch exception</a><br/>
            case for catching exception. </li>
    </ul>

    <h3>more samples</h3>

    <ul>
        <li><a href="forms">form samples</a><br/>
            more form generation using Tuum/Form helpers.</li>
        <li><a href="objGraph">object graph</a><br/>
            view object graph of $responder using koriym/printo</li>
    </ul>

</div>

<?php $this->stop(); ?>