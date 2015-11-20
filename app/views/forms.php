<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\Form\Lists\Lists;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');
$inputs = $view->setInputs(['old' => 'this value is set as old-inputs'])->inputs;
$forms = $view->forms->withClass('form-control');
$dates = $view->dates->setClass('form-control');


?>

<h1>Form Samples</h1>

<h2>simple forms</h2>

<dl class="dl-horizontal">

    <dt>text inputs</dt>
    <dd><?= $forms->text('text', 'text-value'); ?></dd>

    <dt>placeholder</dt>
    <dd><?= $forms->text('text', '')->placeholder('overwrite this water-mark text'); ?></dd>

    <dd>&nbsp;</dd>

    <dt>from $inputs</dt>
    <dd>
        <span class="text-info" >using $inputs object to set the value.</span>
        <input type="text" name="old" id="old" value="<?= $inputs->get('old', 'over-written'); ?>" class="form-control" />
        <label for="old" ><span class="text-info" >the value in the input field is automatically taken from $inputs object.</span></label>
        <?= $forms->text('old', 'over-written'); ?>
        </dd>

</dl>

<h3>other than text input</h3>

<dl class="dl-horizontal">

    <dt>date inputs</dt>
    <dd><?= $forms->date('date', date('Y-m-d')); ?></dd>

    <dt>file inputs</dt>
    <dd><?= $forms->file('file-name'); ?></dd>

    <dt>radio/check</dt>
    <dd><label><?= $forms->radio('radio', 'radio'); ?> radio button</label>&nbsp;
        <label><?= $forms->checkbox('check', 'check'); ?>check box</label></dd>

</dl>

<h2>Lists</h2>

<dl class="dl-horizontal">

    <dt>radio list</dt>
    <dd><span class="text-info">radio list of default output from __toString(). </span>
        <?= $forms->radioList('radio-list', ['1' => 'radio', '2'=>'list'], 2); ?></dd>

    <dt>checkbox list</dt>
    <dd><span class="text-info">formatting checkbox list by a loop.</span><br/>
        <?php $list = $forms->checkList('check-list', ['1' => 'check', '2' => 'box', '3'=>'list'], [1, 3]);
        foreach($list as $key => $f) {
            echo "<label>◀{$f} {$list->getLabel($key)}▶</label> &nbsp;";
        }
        ?></dd>

</dl>

<h2>date and time</h2>

<dl class="dl-horizontal">

    <dt>normal date</dt>
    <dd><?= $dates
            ->setMonth(Lists::months()->useFullText())
            ->dateYMD('date', date('Y-m-d'), '%2$s %3$s, %1$s')->resetWidth(); ?></dd>

    <dt>in Japanese</dt>
    <dd><?= $dates
            ->setYear(Lists::years(1900, 2010, 10)->useJpnGenGou())
            ->setMonth(Lists::months()->usePrintFormat('%s月'))
            ->setDay(Lists::days()->usePrintFormat('%s日'))
            ->dateYMD('date', date('Y-m-d'), '%s %s %s')
            ->resetWidth(); ?></dd>

    <dt>time</dt>
    <dd><?= $dates
            ->setMinute(Lists::minutes(0, 60, 1))
            ->setSecond(Lists::seconds(0, 60, 10))
            ->timeHis('time', date('H:i:s'))->resetWidth(); ?></dd>
</dl>