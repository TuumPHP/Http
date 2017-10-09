<?php

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var ViewHelper $view */
/** @var string $name */

$errorClass = $view->errors->ifExists($name, null, ' has-error');
$label = isset($label) ? $label : $name . ' list';

?>

<div class="form-group<?= $errorClass ?>">
    <label for="<?= $name ?>"><?= $label ?>:</label>
    <ul class="list-unstyled">
        <?php foreach($list as $value => $label):
            $checked = $view->inputs->exists($name) 
                ? $view->inputs->ifExists($name, $value, ' checked')
                : $view->data->ifExists($name, $value, ' checked');
            ?>

            <li><label><input type="checkbox" name="<?= $name ?>[]" value="<?= $value ?>"<?= $checked ?>> <?= $label ?></label></li>

        <?php endforeach; ?>
    </ul>
    <?= $view->errors()->p($name) ?>
</div>
