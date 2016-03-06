<!-- login form -->
<form class="navbar-form navbar-left" role="search" action="/login" method="post">
<div class="form-group">
    <input type="text" name="login" class="form-control" placeholder="User: <?= $view->data()->login ?>">
</div>
<button type="submit" class="btn btn-default">Login</button>
</form>