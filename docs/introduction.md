Introduction
============

A framework agnostic component to construct a response, and more. 

### License

MIT License

### PSRs

* Comform to PSR-1, PSR-2, PSR-4, and 
* uses PSR-7, and PSR-11. 

### Who needs `Tuum/Respond`?

I love the simplicity of Micro Framework, such as Slim 3. But when I want to use them to build an ordinary HTML based web site, I found it lacks some features that automate some tedious  work. 

`Tuum/Respond` provides these missing features such as,

* automatically passes flash messages to templates,
* easily passes input values to HTML forms, 
* standard error templates, 

and some more. 


Installation
-------

### Installation

Please use composer to install Tuum/Respond

```sh
$ composer require "tuum/respond:^3.0"
```

### Demo

The repository includes a demo site to demonstrate what Tuum/Respond can do.
To see the demo site;

1. run `composer install`,
2. change directory to `/public`,
3. run `php -S localhost:8000 index.php`, and
4. access `localhost:8000` via browser.

### Documentation

This documentation is accessible via the demo site above. 

### Demo With `Slim 3`

There is a `asaokamei/slim-tuum` repository to start a new project using [Slim 3 Framework](https://www.slimframework.com/), which also has a more demo site. 


Sample Code
-----------

Assume you have already built a responder, which can be accessible via `$this->responder`... 
Rendering a template is easy. 

```php
$app->get('/{name}', function($req, $res, $args) {
	$name = $args['name'];
	return $this->responder
		->view($req, $res)
		->setSuccess('Welcome, ' . $name)
		->render('welcome');
});
```

It is also very easy to redirect with messages and form inputs saved in flash session to be retrieved in the subsequent request. 

```php
$app->post('/check', function($req, $res, $args) {
	$post = $req->getParsedBody();
	return $this->responder
		->redirect($req, $res)
		->setInput($post)
		->setError('Sorry, always returns as error...')
		->toPath('/form');
});
```

In the form request, the input data and error message are automatically restored from flash session and passed to template. 

```php
$app->get('/form', function($req, $res, $args) {
	return $this->responder
		->view($req, $res)
		->render('form');
});
```


