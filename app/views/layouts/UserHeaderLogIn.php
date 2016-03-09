<?php
/** @var ViewHelper $view */
use Tuum\Respond\Service\ViewHelper;

?>
<!-- login form -->
<form class="navbar-form navbar-left" role="search" action="/login" method="post">
    <span class="text-primary">LogIn: <strong><?= isset($login) ? $view->escape->escape($login): '???' ?></strong></span>
    <div class="form-group">
        <input type="hidden" name="logout" value="">
    </div>
    <button type="submit" class="btn btn-default">Logout</button>
</form>