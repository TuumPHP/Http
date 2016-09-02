<?php
/** @var Template $this */
/** @var ViewHelper $view */

use League\Plates\Template\Template;
use Tuum\Form\Components\NavBar;
use Tuum\Respond\Service\ViewHelper;
use Zend\Diactoros\UploadedFile;

$this->layout('layouts/layout', [
    'nav' => new NavBar('errors', 'forbidden'),
]);
?>

<?php $this->start('contents'); ?>

<h1>Forbidden Errors</h1>

<p>The form below do not have the C.R.S.F. token to cause forbidden error.</p>

<form method="post" action="forbidden.php">

    <input type="submit" value="generate forbidden error" class="btn btn-primary">

</form>
<?php $this->stop(); ?>
