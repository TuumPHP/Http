<?php
/** @var Template $this */
/** @var DataView $view */
use League\Plates\Template\Template;
use Tuum\Form\DataView;

$data = $this->data;
$this->layout('layouts/layout', $data);

?>

<?php $this->start('contents'); ?>

<h1>Generic Error</h1>
<p>It's a generic ERROR!</p>
<p>maybe uncaught exception?</p>
<p><a href="/">start from the beginning!</a></p>

<?php if (isset($exception) && $exception instanceof Exception) : ?>

    <hr>
    <dl class="dl-horizontal">
        <dt>File:</dt>
        <dd><?= $exception->getFile() ?></dd>
        <dt>Line: </dt>
        <dd><?= $exception->getLine() ?></dd>
        <dt>Message:</dt>
        <dd><?= $exception->getMessage() ?></dd>
        <dt>Trace:</dt>
        <dd><pre><?= $exception->getTraceAsString() ?></pre></dd>
    </dl>
    
    
    
<?php endif; ?>

<?php $this->stop(); ?>