Tuum/Respond
=========

`Tuum/Respond` is a framework agnostic module to simplify PSR-7 response object composition. Helps to build Post-Redirect-Get pattern and similar responses. 

`Tuum/Respond` can be used with PSR-7 based middlewares and micro-frameworks to compliment extra functionalities for developing ordinary web sites. 

### License

*	MIT license

### PSR

*   PSR-1, PSR-2, PSR-4, and PSR-7.


### Installation and Demo Site

To install `Tuum/Respond`, use the composer. 

```sh
$ composer require "tuum/respond"
```

To see `Tuum/Respond` working in a sample site, use git and PHP's internal server at public folder as;

```sh
$ git clone https://github.com/TuumPHP/Respond
$ cd Respond
$ composer update
$ cd public
$ php -S localhost:8888
```

and access ```localhost:8888``` by any browser. The sample site uses external bootstrap css and javascript. 

### Dependencies

`Tuum/Respond` depends on the following packages. 

*	[psr/http-message](http://www.php-fig.org/psr/psr-7/), the PSR-7 specification,
*  [Aura/Session]() for managing session and flash storage,
*  [Tuum/Form](https://github.com/TuumPHP/Form) for html form elements and data helpers, 

> Yep, uses home grown form helpers (;´д｀)

and for development, 

*   [Zendframework/Zend-Diactoros](https://github.com/zendframework/zend-diactoros) as a default PSR-7 objects,
*   [Twig](http://twig.sensiolabs.org/),
*   [Tuum/View](https://github.com/TuumPHP/View) for rendering a PHP as a template.


Overview
--------

### Accessing Responder Object

There are two ways to access the responder object: by injecting the responder object, or by storing the responder object in $request's attribute. 

Here's an example for rendering a template. 

```php
// use $responder object to render index page. 
$app->get('/' function ($request) use($responder) {
    return $responder->view($request , $response)
        ->asView('index');
});
```

To use `Respond` class, set the responder object in prior to of using. 

```php
// set $responder object. 
$request = Respond::withResponder($request, $responder);


// use Respond class to access responder object. 
$app->get('/jump', $jump = function($request, $response) {
	return Respond::view($request, $response)
	    ->asView('jump'); // with the 'welcome!' message.
});
```

### Post-Redirect-Get Pattern

The Responder simplifies implementing Post-Redirect-Get pattern by saving data in session's flash data and access it across http requests. For instance, 

```php
// redirects to /jumped.
$app->get('/jumper', function($request, $response) {
	return Respond::redirect($request, $response)
        ->withMessage('redirected back!')
        ->withInputData(['jumped' => 'redirected text'])
        ->withInputErrors(['jumped' => 'redirected error message'])
	    ->toPath('/jumped');
	});
```

Accessing `/jumper` will redirect to `/jump` with the message __"redirected back!"__ and other data. These data are retrieved in the subsequent request, and passed to the view automatically. 

> looks familiar API? I like Laravel very much!

### Prepare View

There's another way of displaying view with extra information. This example shows when accessing '/jumped' path, it draws a page using `$jump` closure. But in prior to the $jump, it sets various data to $responder to be drawn in the view. 

```php
$app->get('/jumped', function($request, $response) use ($jump) {
    $request = Respond::withViewData($request, function(ViewData $view) {
        $view->success('redrawn form!');
        $view->inputData(['jumped' => 'redrawn text']);
        $view->inputErrors(['jumped' => 'redrawn error message']);
        return $view;
    });
    return $jump($request, $response);
});
```

Responder
---------

The respnder object is composed of 3 responders, `View`, `Redirect`, and `Error`, as well as `SessionStorage` object. 

*   `View`: to create a response with a view body. example: `$view->asView('template/name');`
*   `Redirect`: to create a redirect response. example: `$redirect->toPath('path/to');`
*   `Error`: to create response with error status and view. example: `$error->notFound();`
*   `SessionStorage`: manages session and flash data, which is essentially the Aura.Session's segment. example: `$session->setFlash('key', 'value');`. 

The constructor looks like;

```php
$responder = (new Responder(new View, new Redirect, new Error))
	->withSession($session);
```


### Responder::builder

But the easiest way, currently, to build a responder object is to use `Responder::build` method, which takes, `ViewerInterface`, `SessionStorageInterface` object, and some configuration array for errors. 

```php
$view    = TwigViewer::forge(__DIR__ . '/twigs');
$session = SessionStorage::forge('sample');
$error   = ErrorView::forge($view, [
    'default' => 'errors/error',
    'status'  => [
        404 => 'errors/notFound',
    ],
]);
$content_file = 'layouts/contents';
$responder = Responder::build($view, $error, $content_file)
    ->withResponse(new Response())
    ->withSession($session);
```

#### setting `$response` object

Responders takes `$request` and `$response` objects as arguments. To omit the second `$response` object, set it using `withResponse` method in prior to using it. 

```php
$responder = $responder->withResponse($response);
return $responder->view($request)->asView('index');
```

> Responder needs a `$response` object to return since it does not know how to construct a response object. (as it being a framework agnostic module).

#### using `Respond` class

As noted before, `Respond` class offers a convenient way to access responders using __static method__. Use `Respond::withResponder` method to set the responder as an attribute of a `$request` object. 

```php
$request = Respond::withResponder($request, $responder);
```

what it is doing inside is this. 

```php
$request = $request->withAttribute(Responder::class, $responder);
```


You can access the responder, or each of resopnders as:

```php
$responder = Resopnd::getResponder($request);
$view      = Resopnd::view($request, $response);
$redirect  = Resopnd::view($request, $response);
$error     = Resopnd::view($request, $response);
$session   = Resopnd::view($request);
```

### `ViewData` Object

`ViewData` object is a DTO (data-transfer-object) passed by various responders, as well as from a request to next request by saving the object into session's flash. 

There are several methods to set its value.  

#### `withViewData` method

All the responder objects have `withViewData` method
All of `$responder` and associated responder objects has 

```php
$responder = $responder->withViewData(
	function(ViewData $view) {
		$view->success('success message');
		$view->inputData(['key' => 'value']);
		$view->inputError(['key' => 'some error']);
		return $view;
});
```

As for Respond class, it works like,

```php
$request = Respond::withViewData($respond, 
	function(ViewData $view) {
		return $view;
});
```


#### shared API

Furthermore, the View, Redirect, and Error responders share the same API to manage ViewData, that are:

```php
Respond::view($request)
    ->with('name', 'data')
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->withInputError(['key' => 'value'])
    ->withInputData(['key' => 'some error'])
    ->asView('view-file');
```

* [ ] the API is subject to change... 

### View Responder

`View` responder creates basic text, json, or html responces. 

```php
use Tuum\Respond\Respond;
Respond::view($request)->asText('Hello World');
Respond::view($request)->asJson(['Hello' => 'World']);
Respond::view($request)->asHtml('<h1>Hello World</h1>');
Respond::view($request)->asDownload($fp, 'some.dat');
Respond::view($request)->asFileContents('tuum.pdf', 'application/pdf');
Respond::view($request)->asContent('<h1>My Content</h1>');
```

#### content file

The `asContent` method will render any text content within a template layout if `$content_file` is set. The content file may look like, 

```php
{% extends "layouts/layout.twig" %}

{% block content %}

    {{ contents|raw }}

{% endblock %}
```

which must have the `contents`. 

### Redirect Responder

The `Redirect` responder creates redirect responce to uri, path, base-path, or referrer.

```php
Respond::redirect($request)->toAbsoluteUri($request->getUri()->withPath('jump/to'));
Respond::redirect($request)->toPath('jump/to');
Respond::redirect($request)->toBasePath('to');
Respond::redirect($request)->toReferrer();
```

### Error Responder

The `Error` responder simply converts the http status code to a template file, and generates the view. 

```php
Respond::error($request)->forbidden();     // 403: access denied
Respond::error($request)->unauthorized();  // 401: unauthorized
Respond::error($request)->notFound();      // 404: file not found
Respond::error($request)->asView($status); // error $status
```

* [ ] the predefined error methods may change...




# HERE HERE HERE 

to-be-written

View, Template, and `ViewerInterface`
-------------------------------------

### ViewerInterface

### ErrorViewInterface

### Twig Template Files

### Tuum/View Template Files

### Form Helpers

### ErrorView object

`ErrorViewInterface` is essentially the same as `ViewerInterface`, except that the object is expected to take the status code from the `ViewData`, and finds the template file for the code, and renders it. 

```php
$error = ErrorView::forge(
    ViewStream::forge($error_view_dir), [
    'default' => 'errors/error',
    'status' => [
        Error::FILE_NOT_FOUND => 'errors/notFound',
        Error::FORBIDDEN      => 'errors/forbidden',
    ],
]);
```

*   The first argument is another `ViewerInterface` object to render the template for the given code. 
*   second argument is an options array, which has
    *   **default**: default error view name,
    *   **status**: error code and associated view name.



Other Services
------


### SessionStorageInterface

```SessionStorageInterface``` provides ways to access session and flash data storage, whose API is taken from `Aura.Session`'s Segment class. 

The default implementation uses the Aura.Session. 

```php
use Tuum\Respond\Service\SessionStorage;

$session = SessionStorage::forge('some-name');
$responder = $responder->withSession($session);

$response = $next($request, $response); // call next
$session->commit();
```


Helpers
-------

Helper classes helps to manage Psr-7 http message objects. 


### ReqBuilder

*   `createFromPath`: static method to construct a request object. 
*   `createFromGlobal`: another static method that constructs a request object. 


### ReqAttr

public static methods. 

*   ```withBasePath``` / ```getBasePath``` / ```getPathInfo```: manage a base path. pathInfo is the remaining of the entire path minus the base path. 
*   ```withReferrer``` / ```getReferrer```: getReferer returns a path set by withReferrer, or ```$_SEVER['HTTP_REFERER']```. 


### ResponseHelper

*	`isOK(ResponseInterface $response): bool`: 
* 	`isRedirect(ResponseInterface $response): bool`: 
* 	`isInformational(ResponseInterface $response): bool`: 
* 	`isSuccess(ResponseInterface $response): bool`: 
* 	`isRedirection(ResponseInterface $response): bool`: 
* 	`isClientError(ResponseInterface $response): bool`: 
* 	`isServerError(ResponseInterface $response): bool`: 
* 	`isError(ResponseInterface $response): bool`: 
*  `getLocation(ResponseInterface $response): string`:
*  `fill(ResponseInterface $response, string|resource $input, int $status, array $header): ResponseInterface `: 

Views and Template
-----

### ViewData Class

Turned out that this ViewData class is one of the center piece of this package, by managing data used for rendering a view template. 

The ViewData is the core of the responders which is used to transfer data between requests as well as from request to view's renderer. 


### Tuum/Form

Tuum/Form provides helper objects which (not surprisingly) correspond to each of the data in the `ViewData`. All the helpers are combined into `$view` object in the template file. 

```php
<?php
/** @var Renderer $this */
/** @var DataView $view */
use Tuum\Form\DataView;
use Tuum\View\Renderer;

$this->setLayout('layouts/layout');
$data = $view->data;
$forms = $view->forms;
$message = $view->message;
$inputs = $view->inputs;

?>

<?= $forms->text('jumped', $data->jumped)->id()->class('form-control'); ?>
<?= $errors->get('jumped'); ?>
```

The example code shows how to use `$forms`, `$data`, and `$errors`. 

But what about `$inputs`? It is in the `$form` object. If the (old) `$inputs->get('jumped')` value exist, the value is used instead of the given `$data->jumped` value. 