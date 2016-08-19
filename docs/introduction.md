Introduction
====

`Tuum/Respond` provides a __View layer__ (as in MVC2 architecture) for various PSR-7 based micro-frameworks, 
such as [Slim 3](http://www.slimframework.com) and [Zend-Expressive](https://zendframework.github.io/zend-expressive/).
It helps to compose a response object for developing a __traditional web site__ 
(html rendered at server) by simplifying the popular techniques, such as:
    
* Post-Redirect-Get pattern,
* use of object as Presenter (or ViewModel), 
* templates for errors (forbidden, etc.).


### License

*	MIT license

### PSR

*   PSR-1, PSR-2, PSR-4, and PSR-7.


### Installation

To install `Tuum/Respond`, use the composer. 

```sh
$ composer require "tuum/respond:^3.0"
```

### Demo

To see `Tuum/Respond` working in a sample site, use git and PHP's internal server at public folder as;

```sh
$ git clone https://github.com/TuumPHP/Respond
$ cd Respond
$ git checkout 3.x-dev
$ composer install
$ cd public
$ php -S localhost:8888 index.php
```

and access ```localhost:8888``` by any browser. The sample site uses external bootstrap css and javascript. 
