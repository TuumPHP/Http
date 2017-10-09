<?php

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

/** @var Template $this */
/** @var ViewHelper $view */

// echo $view->message->onlyOne();
// this is equivalent to

$classes = [
    'message' => 'success',
    'alert' => 'info',
    'error' => 'danger',
];
?>
<?php if ($message = $view->message->findMostSerious()): ?>
    <div class="alert alert-<?= $classes[$message['type']] ?>"><?= $message['message'] ?></div>
<?php endif;?>
