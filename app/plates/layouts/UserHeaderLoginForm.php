<!-- login form -->
<form class="navbar-form navbar-left" role="search" action="/login" method="post">
    <input type="hidden" name="_token" value="<?= $view->attributes('_token');?>" >
    <div class="form-group">
        <input type="text" name="login" class="form-control" placeholder="user name">
    </div>
    <button type="submit" class="btn btn-default">Login</button>
</form>