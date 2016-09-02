<?php
/** @var Template $this */
/** @var ViewHelper $view */
use League\Plates\Template\Template;
use Tuum\Respond\Service\ViewHelper;

?>
<!-- login form -->
<form class="navbar-form navbar-left" role="search" action="/login" method="post">
    <input type="hidden" name="_token" value="<?= $view->attributes('_token');?>" >
    <span class="text-primary">LogIn: <strong><?= isset($login) ? $this->escape($login): '???' ?></strong></span>
    <div class="form-group">
        <input type="hidden" name="logout" value="">
    </div>
    <button type="submit" class="btn btn-default">Logout</button>
</form>