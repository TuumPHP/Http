<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

$this->layout('layouts/layout');
$forms = $view->forms()->withClass('form-control');

?>

<?php $this->start('contents'); ?>

<h1>Let's Jump!!</h1>

<?= $view->message->onlyOne(); ?>

<p>This sample shows how to create a form input and shows the error message from the redirection.</p>

<h3>Sample Form - redirect pattern</h3>

<div style="margin-left: 2em;">

    <?= $forms->open()->action('jumper')->method('post'); ?>

    <?=
    $forms->formGroup(
        $forms->label('some text here:', 'jumped'),
        $forms->text('jumper', $view->data->raw('jumped'))->id(),
        $view->errors->p('jumped')
    )->class($view->errors->exists('jumped') ? 'has-error' : null);
    ?>

    <?=
    $forms->formGroup(
        $forms->label('some date here:', 'date'),
        $forms->date('date', 'some date')->id()->width('12em'),
        $view->errors->p('date')
    )->class($view->errors->exists('date') ? 'has-error' : null);
    ?>

    <label>your gender</label>
    <?=
    $forms->formGroup(
        $forms->radio('gender', '1')->label('male')->id(),
        $forms->radio('gender', '2')->label('female')->id(),
        $forms->radio('gender', '3')->label('other')->id(),
        $view->errors->p('date')
    )->class($view->errors->exists('date') ? 'has-error' : null);
    ?>

    <label>your movie</label>
    <?=
    $forms->formGroup(
        $forms->checkList('movie', [
            '1' => 'star wars',
            '2' => 'star trek',
            '3' => 'yamato',
        ]),
        $view->errors->p('movie')
    )->class($view->errors->exists('movie') ? 'has-error' : null);
    ?>

    <label>are you happy? </label>
    <?=
    $forms->formGroup(
        $forms->checkbox('happy', 'h'),
        $view->errors->p('happy')
    )->class($view->errors->exists('happy') ? 'has-error' : null);
    ?>

    <?= $forms->submit('jump!')->class('btn btn-primary'); ?>&nbsp;
    <input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default"/>

    <?= $forms->close(); ?>

</div>

<?php $this->stop(); ?>