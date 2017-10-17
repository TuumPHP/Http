<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

$this->layout('layouts/layout');
?>

<?php $this->start('contents'); ?>

<h1>Forbidden Errors</h1>

<?= $view->message; ?>


<div class="col-sm-6">

    <h2>Form without CSRF Token</h2>

    <p>The form below do not have the C.R.S.F. token to cause forbidden error.</p>

    <form method="post">

        <input type="submit" value="generate forbidden error" class="btn btn-primary">

    </form>

</div>


<div class="col-sm-6">

    <h2>Form with CSRF Token</h2>

    <p>form below submits form with CSRF token. </p>

    <form method="post">

        <input type="hidden" name="_token" value="<?= $view->attributes('_token') ?>">
        <input type="submit" value="submit form with CSRF token" class="btn btn-primary">

    </form>

</div>

<?php $this->stop(); ?>
