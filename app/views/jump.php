<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');
$forms = $view->forms->withClass('form-control');

?>

<h1>Let's Jump!!</h1>

<?= $view->message->onlyOne(); ?>

<p>This sample shows how to create a form input and shows the error message from the redirection.</p>

<h3>Sample Form - redirect pattern</h3>

<div style="margin-left: 2em;">

    <?= $forms->open()->action('jumper')->method('post'); ?>

    <?=
    $forms->formGroup(
        $forms->label('some text here:', 'jumped'),
        $forms->text('jumper', 'original text')->id(),
        $view->errors->p('jumped')
    )->class($view->errors->exists('jumped') ? 'has-error' : null);
    ?>
    <?= $forms->submit('jump!')->class('btn btn-primary'); ?>&nbsp;
    <input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default"/>

    <?= $forms->close(); ?>

</div>

