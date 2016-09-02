<?php
/** @var Template $this */
/** @var DataView $view */
use League\Plates\Template\Template;
use Tuum\Form\Components\NavBar;
use Tuum\Form\DataView;

$data = $this->data;
if (!isset($nav)) {
    $data['nav'] = new NavBar('errors', 'general');
}
$this->layout('layouts/layout', $data);

?>

<?php $this->start('contents'); ?>

<h1>Generic Error</h1>
<p>It's a generic ERROR!</p>
<p>maybe uncaught exception?</p>
<p><a href="/">start from the beginning!</a></p>

<?php $this->stop(); ?>