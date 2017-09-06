<?php
/** @var Template $this */
/** @var ViewHelper $view */

use App\App\Controller\LoginPresenter;
use League\Plates\Template\Template;
use Tuum\Form\Components\BreadCrumb;
use Tuum\Form\Components\NavBar;
use Tuum\Respond\Service\ViewHelper;

$nav  = isset($nav) ? $nav : new NavBar(null);
if (isset($bread)) {
    $bread->add('Home', '/');
} else {
    $bread = null; // no breadcrumb!
}

?>
<!DOCTYPE html>
<!--suppress JSUnresolvedLibraryURL -->
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Tuum/Respond Sample</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/demo.css">

    <!-- Latest compiled and minified JavaScript -->
    <!--suppress JSUnresolvedLibraryURL -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
</head>
<body>

<nav id="header" class="nav navbar-inverse">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="/">Tuum/Respond with Plates</a>
        </div>
        <!-- login form -->
        <?php echo $view->call(LoginPresenter::class); ?>
        <!-- sample menu -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown<?= $nav->m('documents');?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Documents <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li class="<?= $nav->m('documents', 'introduction');?>"><a href="/docs/introduction" >Template and Helpers</a></li>
                        <li class="<?= $nav->m('documents', 'responders');?>"><a href="/docs/responders" >Responders</a></li>
                        <li class="<?= $nav->m('documents', 'template');?>"><a href="/docs/template" >Template</a></li>
                        <li class="<?= $nav->m('documents', 'controller');?>"><a href="/docs/controller" >Controller</a></li>
                    </ul>
                </li>
                <li class="dropdown<?= $nav->m('samples');?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Samples <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li class="<?= $nav->m('samples', 'jump');?>"><a href="/jump">Jump: form and redirect</a></li>
                        <li class="<?= $nav->m('samples', 'upload');?>"><a href="/upload">Upload: uploading files</a></li>
                        <li class="<?= $nav->m('samples', 'content');?>"><a href="/content">Content: html rendering</a></li>
                        <li class="<?= $nav->m('samples', 'forms');?>"><a href="/forms">Forms: html inputs</a></li>
                        <li class="<?= $nav->m('samples', 'objGraph');?>"><a href="/objGraph">Object graph</a></li>
                    </ul>
                </li>
                <li class="dropdown<?= $nav->m('errors');?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Errors <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li class="<?= $nav->m('errors', 'general');?>"><a href="/throw">General Errors</a></li>
                        <li class="<?= $nav->m('errors', 'notFound');?>"><a href="/not-such-file">Not Found Error</a></li>
                        <li class="<?= $nav->m('errors', 'forbidden');?>"><a href="/forbidden">Forbidden Error</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="content">
    <?php if (isset($bread)): ?>
    <ol class="breadcrumb">
        <?php foreach ($bread as $url => $label): ?>
            <?php
            if ($bread->isLast()) {
                echo "<li class='active'>{$label}</li>";
            } else {
                echo "<li><a href='{$url}' >{$label}</a></li>";
            }
            ?>
        <?php endforeach; ?>
    </ol>
    <?php endif; ?>

    <div class="col-md-12">

        <?= $this->section('contents'); ?>

    </div>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>

</div>

<nav id="footer" class="nav navbar-fixed-bottom navbar-default">
    <div class="container">
        <strong style="font-size: 1.2em;">Tuum/Respond.</strong>
        <p><em>Tuum</em> means 'yours' in Latin. </p>
    </div>
</nav>

</body>
</html>