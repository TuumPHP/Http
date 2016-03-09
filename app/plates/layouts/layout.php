<?php
/** @var Template $this */
/** @var ViewHelper $view */

use App\App\LoginPresenter;
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;
use Tuum\View\Renderer;

?>
<!DOCTYPE html>
<!--suppress JSUnresolvedLibraryURL -->
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Tuum/Respond Sample</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

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
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Samples <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/jump">Jump: form and redirect</a></li>
                        <li><a href="/upload">Upload: uploading files</a></li>
                        <li><a href="/content">Content: html rendering</a></li>
                        <li><a href="/objGraph">Object graph</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/not-such-file">Not Found Error</a></li>
                        <li><a href="/throw">Uncaught Exception</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="content">

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
        <h4>Tuum/Respond.</h4>
        <p><em>Tuum</em> means 'yours' in Latin. <br/>
            <?= $view->request()->getMethod() ?>: <?= $view->request()->getUri() ?></p>
        <p>&nbsp;</p>
    </div>
</nav>

</body>
</html>