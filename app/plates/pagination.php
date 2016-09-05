<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Form\Components\NavBar;
use Tuum\Respond\Service\ViewHelper;

$this->layout('layouts/layout', [
    'nav' => new NavBar('samples', 'pagination'),
]);

$input = $view->data->extractKey('input');
$found = $view->data->extractKey('found');
?>

<?php $this->start('contents'); ?>

    <h1>Pagination Sample</h1>

<?= $view->message->onlyOne(); ?>

    <div class="col-md-3">
        <form name="pagination" method="get" action="">
            <label>Key: <input type="text" name="key" value="<?= $input->get('key'); ?>"
                               style="width: 5em;"></label><br/>
            <label>Love: <input type="checkbox" name="love" value="love" <?= $input->ifExists('love', 'love',
                                                                                              ' checked'); ?>></label><br/>
            <label>Total: <input type="text" name="total" value="<?= $input->get('total'); ?>"
                                 style="width: 3em;"></label><br/>
            <input type="submit" value="show pagination" class="btn btn-primary">
        </form>
    </div>

    <style type="text/css">
        div.found {
            display: inline-block;
            margin-right: .5em;
        }
    </style>

    <div class="col-md-9">

        <?php foreach ($found->getKeys() as $key): ?>

            <div class="found"><?= $found->get($key); ?></div>
            <?= ($key + 1) % 5 ? '' : '<br/>'; ?>

        <?php endforeach; ?>

        <p>&nbsp;</p>

        <?= $view->data->get('pages'); ?>

    </div>

<?php $this->stop(); ?>