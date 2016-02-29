<?php
/** @var Renderer $this */
/** @var ViewHelper $view */

use Tuum\Respond\Service\ViewHelper;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');

?>

<style type="text/css">
    div.broadcast {
        margin: 1em;
        padding: 0 1em 1em 1em;
        background-color: #f0f0f0;
        border: 2px solid #cccccc;
        border-radius: 10px;
    }
    li {
        margin-top: .5em;
    }
    li>a {
        font-weight:bold;
    }
</style>

<div class="broadcast">

    <h1>Tuum/Respond</h1>

    <p>A plug-in responder module for composing PSR-7 responses. </p>
    <p>This module provides <strong>view functions (as in MVC concept)</strong>
        to implement Post-Redirect-Get pattern and similar techniques.
        Many of the PSR-7 based micro-frameworks would become suitable for
        developing an ordinary web site.</p>
    <ul>
        <li>PSR-1, PSR-2, PSR-4, and PSR-7</li>
        <li>MIT License</li>
    </ul>

</div>

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
