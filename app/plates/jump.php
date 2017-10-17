<?php

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var ViewHelper $view */

$this->layout('layouts/layout');
$forms = $view->forms()->withClass('form-control');

?>

<?php $this->start('contents'); ?>

<h1>Let's Jump!!</h1>

<?php $this->insert('/components/messages'); ?>

<p>This sample shows how to create a form input and shows the error message from the redirection.</p>

<h3>Sample Form - redirect pattern</h3>

<div style="margin-left: 2em;">

    <?= $forms->open()->action('')->method('post'); ?>
    <input type="hidden" name="_token" value="<?= $view->attributes('_token');?>">

    <?php $this->insert('components/text', ['name' => 'jumper', 'label' => 'some text',]); ?>
    
    <?php $this->insert('components/date', [
            'name' => 'date', 
            'label' => 'date here',
            'attr' => [
                'style' => 'width: 12em;',
            ]]); ?>
    
    <?php $this->insert('components/radioList', [
        'name' => 'gender',
        'label' => 'your gender',
        'list' => [
            '1' => 'male',
            '2' => 'female',
            '3' => 'other',
        ]]); ?>

    <?php $this->insert('components/checkList', [
            'name' => 'movie',
            'label' => 'movie list',
            'list' => [
                '1' => 'star wars',
                '2' => 'star trek',
                '3' => 'yamato',
            ]]); ?>

    <div class="form-inline">
        <label>are you happy? <?= $forms->checkbox('happy', 'h') ?></label>
        <?= $view->errors->p('happy') ?>
    </div>

    <?= $forms->submit('jump!')->class('btn btn-primary'); ?>&nbsp;
    <input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default"/>

    <?= $forms->close(); ?>

</div>

<?php $this->stop(); ?>