Tuum/Respond
=========

`Tuum/Respond` is a framework agnostic module to simplify PSR-7 response object composition, which can be used with many of the PSR-7 based middlewares and micro-frameworks. 

This module helps to develop an ordinary web site which requires Post-Redirect-Get pattern and similar techniques. 

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
$ composer install
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

The `$responder` object can be used to render a template file;

```php
// use $responder object to render index page. 
$app->get('/' function($request, $response) use($responder) {
    return $responder->view($request , $response)
        ->asView('index');
});
```

### Respond Class

The `Respond` class offers an easier way to manage the responder object;

```php
// set $responder object in a middleware or somewhere. 
$request = Respond::withResponder($request, $responder);

// use Respond class to access responder object. 
$jump = function($request, $response) {
	return Respond::view($request, $response)
	    ->asView('jump'); // with the 'welcome!' message.
};
$app->get('/jump', $jump);
```

The `$responder` object is set as an attribute of the `$request` object, and accessible anywhere using `Respond`'s static method. 

> The responder object is immutable. This seems to be the safest way to carry around the modified $responder object. But it is also possible to inject the responder object, as well. 

### Post-Redirect-Get Pattern

The Responder simplifies implementing Post-Redirect-Get pattern by saving data in session's flash data and access it across http requests. For instance, 

```php
// redirects to /jumped.
$app->get('/jumper', function($request, $response) {
	return Respond::redirect($request, $response)
        ->withMessage('redirected back!')
        ->withInputData(['jumped' => 'redirected text'])
        ->withInputErrors(['jumped' => 'redirected error message'])
	    ->toPath('/jump');
	});
```

Accessing `/jumper` will redirect to `/jump` with the message __"redirected back!"__ and other data. These data are retrieved in the subsequent request, and passed to the view automatically. 

> looks familiar API? I like Laravel very much!

### Before the View

There's another way of displaying view with extra information. This example shows when accessing `/jumped` path, it draws a page using the `$jump` closure. But in prior to the rendering, it sets various data to $responder to be drawn in the view. 

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

Responders
---------

There are 3 types of responders, `view`, `redirect`, and `error`, each of which specialized to compose a response object in its own way. 

Also, there is `session` to help manage sessions.

### View Responder

`View` responder creates a response with a view body, such as basic text, json, or html text. 


```php
use Tuum\Respond\Respond;
Respond::view($request)->asView('template/filename');
Respond::view($request)->asText('Hello World');
Respond::view($request)->asJson(['Hello' => 'World']);
Respond::view($request)->asHtml('<h1>Hello World</h1>');
Respond::view($request)->asDownload($fp, 'some.dat');
Respond::view($request)->asFileContents('tuum.pdf', 'application/pdf');
Respond::view($request)->asContent('<h1>My Content</h1>');
```


### Redirect Responder

The `Redirect` responder creates redirect responce to uri, path, base-path, or referrer.

```php
Respond::redirect($request)->toAbsoluteUri(
    $request->getUri()->withPath('jump/to')
);
Respond::redirect($request)->toPath('jump/to');
Respond::redirect($request)->toBasePath('to');
Respond::redirect($request)->toReferrer();
```


### Error Responder

The `Error` responder renders a template file according to the http status code

```php
Respond::error($request)->forbidden();     // 403: access denied
Respond::error($request)->unauthorized();  // 401: unauthorized
Respond::error($request)->notFound();      // 404: file not found
Respond::error($request)->asView($status); // error $status
```

### Session Storage

The `SessionStorageInterface` object is not a responder but managed as part of Responder object. 

```php
Respond::session($request)->set($key, $value);
Respond::session($request)->get($key);
Respond::session($request)->setFlash($key, $value);
Respond::session($request)->getFlash($key, $value);
Respond::session($request)->getFlashNext($key);
Respond::session($request)->validateToken($value);
Respond::session($request)->getToken();
``` 

### `Respond` Class

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
$redirect  = Resopnd::redirect($request, $response);
$error     = Resopnd::error($request, $response);
$session   = Resopnd::session($request);
```

Manipulating `ViewData`
---------

Essentially, the responder modules are desinged for setting data for view.

The central part is `ViewData` object, which acts as data-transfer-object between objects as well as between requests by saving the object into session's flash storage.

There are several methods to set its value.  

### Shared API

The `View`, `Redirect`, and `Error` responders share the same API to manage the `ViewData` object; they are:

```php
Respond::{$name}($request)
    ->withData('name', 'data')
    ->withSuccess('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->withInputData(['key' => 'some error'])
    ->withInputError(['key' => 'value']);
```


### `withViewData` method

All the responder objects have `withViewData` method
All of `$responder` and associated responder objects have;

```php
$responder = $responder->withViewData(
	function(ViewData $view) {
		$view->setSuccess('success message');
		$view->setInputData(['key' => 'value']);
		$view->setInputError(['key' => 'some error']);
		return $view;
});
```

For `Respond` class, the method returns a new `$request` object (due immutability).

```php
$request = Respond::withViewData($request, 
	function(ViewData $view) {
		return $view;
});
```

And other responders;

```php
$$name = Respond::{$name}($request)->withViewData(
	function(ViewData $view) {
		return $view;
});
```

#### ViewData's Methods

The `ViewData` object has following methods. 

setters:

```php
$viewData->setData($key, $value);
$viewData->setInputData([$key => $value]);
$viewData->setInputErrors([$key => $value]);
$viewData->setMessage($message, $level);
$viewData->setSuccess($message);
$viewData->setAlert($message);
$viewData->setError($message);
$viewData->setViewFile($fileName);
$viewData->setStatus($status);
```

getters:

```php
$viewData->getData();
$viewData->getInputData();
$viewData->getInputErrors();
$viewData->getMessage();
$viewData->getViewFile();
$viewData->getStatus();
```

Template and Form Helpers
-----

The template need to understand the `ViewData`'s various information when redering a value. For instance, when redering a form's value, the template should use the value from the ViewData's inputData, instead of the given value. 

The default implementation uses `Tuum/Form` helpers to render information. 


### Tuum/Form

There are following helpers which corresponds each of ViewData's data;

*   **inputs**: for ViewData's `inputData`,
*   **errors**: for ViewData's `inputErrors`,
*   **message**: for ViewData's `message` (i.e. `success`, `alert`, and `error`),
*   **data**: for ViewData's `data`,
*   **forms**: for displaying html form elements using ViewData's `inputData`,


#### TwigViewer

Most of the helpers are accessible via viewData .{helperName}, while value set as `data` can be access directly as normal values for twig. 

```php
{{ viewData.message|raw }}
{{ viewData.inputs.get('user[name]') }}
{{ viewData.forms.text('name', 'value')|raw }}
{{ viewData.errors.get('name')|raw }}
{{ varName }}
```

#### TuumViewer

All the helpers are combined into `$view` object in the template file for `TuumViewer`. 

```php
<?php
use Tuum\Form\DataView;
use Tuum\View\Renderer;

/** @var Renderer $this */
/** @var DataView $view */

$data = $view->data;
$forms = $view->forms;
$message = $view->message;
$inputs = $view->inputs;

?>

<?= $message->onlyOne() ?>
<?= $forms->text('jumped', $data->jumped)->id()->class('form-control'); ?>
<?= $errors->get('jumped'); ?>
```




Constructing Responders
------------------

### Constructing Responder Object

The constructor looks like;

```php
$responder = (new Responder(new View, new Redirect, new Error))
	->withSession($session);
```

But the easier way to build a responder object, currently, is to use `Responder::build` method, which takes, `ViewerInterface`, `SessionStorageInterface` object, and some configuration array for errors. 

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


### View Responder

The `View` Responder takes `ViewerInterface` object, and optionally the template filename for content rendering. 

```php
$view = new Tuum\Respond\Responder\View(
    TwigViewer::forge($twig_root),
    'layout/contents'
);
```

#### content file

The `asContent` method will render any text content within a template layout if `$content_file` is set. The content file **must have a section named "contents"**, which may look like, 

```php
{% extends "layouts/layout.twig" %}

{% block content %}

    {{ contents|raw }}

{% endblock %}
```

which must have the `contents`. 

### Redirect Responder

construction of Redirect responder does not take any arguments; 

```php
$redirect = new Tuum\Respond\Responder\Redirect();
```

### Error Responder

The `Error` responder takes `ErrorViewInterface` object as argument;

```php
$error = new Tuum\Respond\Responder\Error(
    new Tuum\Respond\Service\ErrorView(
        TwigViewer::forge($twig_root)
    )
);
```

The code and method names are defined as public property;

```php
$error->methodStatus = [
    'unauthorized' => self::UNAUTHORIZED,
    'forbidden'    => self::ACCESS_DENIED,
    'notFound'     => self::FILE_NOT_FOUND,
];
```

### `ViewerInterface` Object

This simple interface defines objects that renders a view from template files. 

```php
interface ViewerInterface
{
    /**
     * renders $view and returns a new $response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ViewData               $view
     * @return ResponseInterface
     */
    public function withView(ServerRequestInterface $request, ResponseInterface $response, $view);
}
```

There are two implementations of the `ViewerInterface`: `TwigViewer` for using Twig template and `TuumViewer` for using plain a PHP file as template. 

#### TwigViewer

The `TwigViewer`'s constructor takes the `Twig_Environment` object as argument:

```php
$loader = new Twig_Loader_Filesystem($root_dir);
$twig   = new Twig_Environment($loader, $options);
$view   = new TwigViewer($twig);
```

Where the `$root_dir` is the root of Twig template files, and `$options` is an array to store options. 

Alternatively, you can use `TwigViewer::forge` factory method as well;

```php
use Tuum\Respond\Service\TwigViewer;

$view = TwigViewer::forge($root_dir, $options, 
	function(Twig_Environment $twig) {
		// further configure $twig renderer...
		return $twig;
	});
```

The third closure is an optional argument. 

#### TuumViewer

Easy way to construct `TuumViewer` is to use a factory method. 

```php
use Tuum\Respond\Service\TuumViewer;
use Tuum\View\Renderer;

$view = new TuumViewer(new Renderer($root_dir));

// or 

$view = TuumViewer::forge($root_dir, 
	function(Renderer $renderer) {
		// further configure $twig renderer...
		return $renderer;
	});
```

The second closure is an optional argument. 

### `ErrorViewInterface`

The `ErrorViewInterface` is essentially the same as the `ViewerInterface`,

```php
interface ErrorViewInterface extends ViewerInterface {}
```

 but the interface object is expected to 

1. take the http response status code from ViewData, 
2. finds the template file for the status, 
3. renders the template, and 
4. return a response. 

#### ErrorView object

A factory method is avaible to construct the default `ErrorView` object; 

```php
use Tuum\Respond\Service\ErrorView;

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

Helper classes helps to manage PSR-7 http message objects. 


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

