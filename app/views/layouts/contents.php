<?php
use Tuum\Respond\Service\ViewHelper;

/** @var $view ViewHelper */

$this->setLayout('layouts/layout');
?>
<?= isset($contents) ? $contents: ''; ?>