<?php

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var ViewHelper $view */
/** @var string $name */

$errorClass = $view->errors->ifExists($name, null, ' has-error');
$label = isset($label) ? $label : $name;
$value = $view->inputs->get($name, $view->data->get($name));

?>

<div class="form-group<?= $errorClass ?>">
    <label for="<?= $name ?>"><?= $label ?>:</label>
    <input type="text" id="<?= $name ?>" name="<?= $name ?>" class="form-control" 
           value="<?= $value ?>">
    <?= $view->errors()->p($name) ?>
</div>

