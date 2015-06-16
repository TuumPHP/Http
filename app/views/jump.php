<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');
$forms = $view->forms;

?>

<h1>Let's Jump!!</h1>

<h3>Sample Form</h3>

<?= $view->message; ?>

<?= $forms->open()->action('jumper'); ?>

<?=
$forms->formGroup(
    $forms->label('some text message here:', 'jumped'),
    $forms->text('jumped', 'some message here')->id()->class('form-control'),
    $view->errors->get('jumped')
);
?>
<?= $forms->submit('jump!')->class('btn btn-primary'); ?>&nbsp;
    <input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default" />

<?= $forms->close(); ?>