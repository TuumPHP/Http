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

<?= $forms->text('jumped', 'some message here'); ?>
<?= $view->errors->get('jumped'); ?><br/>
<p><?= $forms->submit('jump!')->class('btn btn-primary'); ?>&nbsp;
<input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default" /></p>

<?= $forms->close(); ?>