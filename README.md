Tuum/Respond
=========

`Tuum/Respond` is a framework agnostic PHP module to 
help construct a PSR-7 response object, and more. 

It provides MVC's "View" functionality as shown in the figure 
below.  

![overview of Tuum/Respond](docs/overview.png)

In this figure, the Controller part is managed by the framework 
and `Tuum/Respond` composes a respond object for views 
(using template), error pages, and redirects. 

```php
$app->add('/',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
        return $responder->view($request, $response)
            ->setSuccess('welcome!')
            ->render('index');
    });
```

### License

*	MIT license

### PSR

*   PSR-1, PSR-2, PSR-4, and PSR-7.

### Who needs `Tuum/Respond`?

Micro frameworks with middleware are very simple yet gives a 
great power on building a web application, but may not 
provide some useful features such as, 

* carries flash messages and csrf token to templates,
* easily handles input values and validation errors, 
* standard error templates, 

With Tuum/Respond, it will be very easy to implement:

* Post-Redirect-Get pattern,
* use of object as Presenter (or ViewModel), 
* filling input values in forms after validation error.


Installation
-------

### Installation and Demo Site

Please use the composer to install `Tuum/Respond`; 

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

### Demo with Slim3

There is a demo with Slim3 framework, 
[slim-tuum](https://github.com/asaokamei/slim-tuum) repository, 
for more realistic demo. 


