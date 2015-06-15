<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

?>
<h1>Let's Jump!!</h1>
<a href="jumper">jump and back!</a>

<h3>from previous request...</h3>
<?= $view->message; ?>
<?= $view->inputs->get('jumped'); ?>
<?= $view->errors->get('jumped'); ?>