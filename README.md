Tuum/Respond
=========

`Tuum/Respond` is a framework agnostic __View module__ (as in MVC2 architecture) for various PSR-7 based micro-frameworks, 
such as [Slim 3](http://www.slimframework.com) and [Zend-Expressive](https://zendframework.github.io/zend-expressive/).

It provides convenient functions for composing a response object, to help developing a __traditional web site__ 
(html rendered at server) by simplifying the popular techniques, such as:

* Post-Redirect-Get pattern,
* use of object as Presenter (or ViewModel), 
* templates for errors (forbidden, etc.).


### License

*	MIT license

### PSR

*   PSR-1, PSR-2, PSR-4, and PSR-7.


### Installation and Demo Site

To install `Tuum/Respond`, use the composer. 

```sh
$ composer require "tuum/respond:^2.0"
```

To see `Tuum/Respond` working in a sample site, use git and PHP's internal server at public folder as;

```sh
$ git clone https://github.com/TuumPHP/Respond
$ cd Respond
$ git checkout 2.x-dev
$ composer install
$ cd public
$ php -S localhost:8888 index.php
```

and access ```localhost:8888``` by any browser. The sample site uses external bootstrap css and javascript. 

### Dependencies

`Tuum/Respond` depends on the following packages. 

*  [psr/http-message](http://www.php-fig.org/psr/psr-7/), the PSR-7 specification,
*  [Aura/Session]() for managing session and flash storage,
*  [Tuum/Form](https://github.com/TuumPHP/Form) for html form elements and data helpers, 

> Yep, uses home grown form helpers (;´д｀)

works with following template engines; 

*   [League/Plates](httphttp://platesphp.com/),
*   [Twig](http://twig.sensiolabs.org/), and maybe
*   [Tuum/View](https://github.com/TuumPHP/View).


