Tuum/Respond
=========

If a full-stack framework is for MVC (Model-View-Controller) pattern,
and if a micro-framework is for C (Controller, or routing and dispatching), 
then `Tuum/Respond` is a module for V (View) to help creating 
a PSR-7 response object. 

### View is more than just a template engine

Most of a typical HTML web site need to:  

- show flash messages saved in session after redirect, 
- retrieve csrf token in template pages,
- show previous input values and validation errors after form validation, 
- respond error pages like not-found, 

and more. 

Using `Tuum/Respond` module with PSR-7 based micro-frameworks, 
such as Slim3 or Expressive, gives these useful functions that 
are often available only in full-stack frameworks. 

### Who needs `Tuum/Respond`?

`Tuum/Respond` is a **module**, not a framework.
I did not want to develop yet-another framework. 
Rather I wanted to utilize existing great micro-frameworks and PSR standards. 

Thus, `Tuum/Respond` is for people who uses PSR based micro-frameworks, 
yet wants/needs to develop a traditional html based web sites.  

About This Package
-----

### License

*	MIT license

### PSR

*   PSR-1, PSR-2, PSR-4, PSR-7, PSR-15, and PSR-17.



Installation
-------

### Installation

Please use the composer to install `Tuum/Respond`; 

```sh
$ composer require "tuum/respond:^4.0"
```

### Demo

The repository includes a sample site to demonstrate what `Tuum/Respond` can do.
To see the site;

1. run `composr create-project "tuum/respond" [proj-dir] "^4.0"`
1. change directory to `[proj-dir]`
1. run `composer install`,
2. change directory to `/public`,
3. run `php -S localhost:8000 index.php`, and
4. access `localhost:8000` via browser.



--------



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


