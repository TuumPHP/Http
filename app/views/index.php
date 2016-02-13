<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
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

<h3>redirect with messages sample</h3>
<ul>
    <li><a href="jump">jump!!!</a></li>
</ul>

<h3>file upload sample</h3>
<ul>
    <li><a href="upload">upload!!!</a></li>
</ul>

<h3>content</h3>
<ul>
    <li><a href="content">content</a></li>
</ul>

<h3>error samples</h3>

<ul>
    <li><a href="not-such-file">not found</a></li>
    <li><a href="throw">catch exception</a> </li>
</ul>

<h3>graphs</h3>

<ul>
    <li><a href="objGraph">object graph</a> </li>
</ul>

<h3>Form Samples</h3>

<ul>
    <li><a href="forms" >form samples</a></li>
</ul>