Tuum/Respond
=========

Tuum/Respond is a framework independent helpers and responders for composing a PSR-7 response object. Helps to build Post-Redirect-Get pattern and similar responses. 

### License

*	MIT license

### PSR

*   PSR-1, PSR-2, and PSR-4.


### Installation and samples

To install Tuum/Respond, use the composer. 

```sh
$ composer require "tuum/respond"
```

To see Tuum/Respond working in a sample site, use git and PHP's internal server at public folder as;

```sh
$ git clone https://github.com/TuumPHP/Respond
$ cd Respond/public
$ php -S localhost:8888 index.php
```

and access ```localhost:8888``` by any browser. The sample site uses external bootstrap css and javascript. 


Overview
--------

Tuum/Respond can be used together with PSR-7 based middlewares and micro-frameworks to compliment extra functionalities for developing ordinary web sites. 

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
$responder = Responder::build($view, $error, 'layouts/contents')
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

### Setting View Data

`ViewData` is a DTO (data-transfer-object) between responders, as well as between requests via session's flash. There are many methods to set and manage the data object. 

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



HERE HERE HERE



View Responder
----

`View` responder creates basic text, json, or html responces. 

```php
use Tuum\Respond\Respond;
Respond::view($request)->asText('Hello World');
Respond::view($request)->asJson(['Hello' => 'World']);
Respond::view($request)->asHtml('<h1>Hello World</h1>');
Respond::view($request)->asDownload($fp, 'some.dat');
Respond::view($request)->asFileContents('tuum.pdf', 'application/pdf');
```

### Using Views (Template)

`View` responder can create responce using view (or template). 

```php
Respond::view($request)
    ->with('name', 'value')
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->asView('view-file');
```

Similarly, `asContent` method will render any text content within a template layout if ```ViewStream``` object implements it. 

```php
Respond::view($request)
    ->asContent('<h1>My Content</h1>');
```

To use Views feature, provide ```ViewStreamInterface``` object to the responders. 


Redirect Responder
----

`Redirect` responder creates redirect responce to uri, path, base-path, or referrer.

```php
Respond::redirect($request)->toAbsoluteUri($request->getUri()->withPath('jump/to'));
Respond::redirect($request)->toPath('jump/to');
Respond::redirect($request)->toBasePath('to');
Respond::redirect($request)->toReferrer();
```

### Passing Data From Redirect To View

Use Redirect and Respond responders to pass data between requests as, 

```php
Respond::redirect($request)
    ->with('extra', 'value')
    ->withInputData(['some' => 'value'])
    ->withInputErrors(['some' => 'error message'])
    ->withError('error-message')
    ->toPath('back/to');
```

then, receive the data as,

```php
Respond::view($request)
    ->asView('some-view'); // all the data from the previous request.
```

The data set by using ```with``` methods will be stored in a session's flash data; the subsequent ```Respond::view``` method will automatically retrieve the flash data and populate them in the template view. 

To enable this feature, provide ```SessionStorageInterface``` object to the responders.  


Error Responder
----

Error responder generates a template view based on the status code by using ```ErrorViewInterface``` object. Set up the error view, then,

```php
Respond::error($request)->forbidden();
```

To enable this feature, provide ```ErrorViewInterface``` object to the responders. 

Services
------

### Services

The Psr-7 http/message does not provide all the necessary functionalities to use the responders. You need to supply services that are defined by following interfaces. 

*   ```SessionStorageInterface``` for session,
*   ```ViewStreamInterface``` for views, and
*   ```ErrorViewInterface```.

optionaly, 

*   ```ContainerInterface``` for containers, 

maybe used to provide services.

### Packages

Currently, Tuum/Respond uses following packages. 

*   [Zendframework/Zend-Diactoros](https://github.com/zendframework/zend-diactoros) as a default Psr-7 objects.
*   [Aura/Session]() for managing session and flash storage.
*   [Tuum/View](https://github.com/TuumPHP/View) for rendering a PHP as a template.
*   [Tuum/Form](https://github.com/TuumPHP/Form) for html form elements and data helpers.
*   [Container-interop/container-interop](https://github.com/auraphp/Aura.Session) for container.

> Yep, uses home grown views and forms (;´д｀)



The responders requires many services to operate. 
These services are stored in ```$request->withAttribute``` method. 

### SessionStorageInterface

```SessionStorageInterface``` provides ways to access session and flash data storage, whose API is taken from Aura.Session's segment. 

#### using default SessionStorage object

The default implementation uses the Aura.Session. 

```php
use Tuum\Respond\Service\SessionStorage;

$session = SessionStorage::forge('some-name');
$responder = $responder->withSession($session);

$response = $next($request, $response); // call next
$session->commit();
```


### ViewStreamInterface

The ```ViewStreamInterface``` extends Psr-7's ```StreamInterface``` to add extra methods for rendering a view/template. 

*   ```withView($view_name, ViewData $data)```: sets the template file name for the view and render data. 
*   ```withContent($view_name, ViewData $data)```: sets the contents of a view and render data. This method maybe used for rendering a static file. 

To respond using ```view``` resopnder, provide a ViewStream object implementing ```ViewStreamInterface``` interface. 

#### using default ViewStream object

The default ViewStream class uses ```Tuum/View``` and ```Tuum/Form``` packages, 

```php
$view = ViewStream::forge($view_dir);
```

where the `$view_dir` points to the root of the view/template directory, such as `__DIR__ . '/views'`.


### ErrorViewInterface

The ```ErrorViewInterface``` is a simplified ViewStreamInterface which can render a view based on status code instead of view name as used in ViewStreamInterface. 

#### using default ErrorView object

To respond using the ```error``` responder, provide an ```ErrorView``` object. To use the default set, 

```php
$error = ErrorView::forge(
    ViewStream::forge($error_view_dir), [
    'default' => 'errors/error',
    'status' => [
        Error::FILE_NOT_FOUND => 'errors/notFound',
        Error::FORBIDDEN      => 'errors/forbidden',
    ],
    'handler' => true,
]);
```

*   The first argument is another ViewStreamInterface object to render a view. 
*   second argument is an options array, which has
    *   **default**: default error view name,
    *   **status**: error code and associated view name,
    *   **handler**: use the `ErrorView` for catching uncaught exceptions using `set_exception_handler`.

> Yes, you can use the ```ErrorView``` object for PHP's ```set_exception_handler``` handle as well:
>
> ```php
> set_exception_handler($error); // catch uncaught exception!!!
> ```

### ContainerInterface

A container maybe used to provide the services to responders. The container must implement ```ContainerInterface``` by Container-interop group. 

Populate the container with the services using interface class name as a key, and set it in the $request using `RequestHelper::withApp` method, as;

```php
$app = new Container(); // some ContainerInterface...
$app->set(Responder::class, $responder);
$request = RequestHelper::withApp($request, $app);
```


Helpers
-------

### Helper Classes

Helper classes helps to manage Psr-7 http message objects. For instance, 

```php
$bool = ResponseHelper::isRedirect($response);
``` 

will evaluate $response to check for redirect response. There are helpers for request and responce. 


### RequestHelper

constants:

*   ```APP_NAME```: 
*   ```BASE_PATH```: key name for basePath. 
*   ```PATH_INFO```: key name for pathInfo. 
*   ```SESSION_MANAGER```: key name for session.
*   ```REFERRER```: key name for referrer path. 

public static methods. 

*   ```createFromPath```: static method to construct a request . 
*   ```withApp``` / ```getApp```: manage application or container (```ContainerInterface```). 
*   ```getService```: a simplified way to get a service from the container. 
*   ```withBasePath``` / ```getBasePath``` / ```getPathInfo```: manage a base path. pathInfo is the remaining of the entire path minus the base path. 
*   ```withSessionMgr``` / ```getSessionMgr```: manage ```SessionStorageInterface``` object. 
*   ```setSession``` / ```getSession```: set and get values to the session. 
*   ```setFlash``` / ```getCurrFlash``` / ```getFlash```: set and get values to the flash memory of the current session. getCurrFlash gets the value set in the current request. 
*   ```withReferrer``` / ```getReferrer```: getReferer returns a path set by withReferrer, or ```$_SEVER['HTTP_REFERER']```. 


### ResponseHelper

to-be-written


Views and Template
-----

To use a view/template system, it has to satisfy the followings:

*   implement ViewStreamInterface,
*   understand ViewData object.

Currently, only the default ViewStream uses Tuum/View and Tuum/Form matches these criteria. 

### ViewData

Turned out that this ViewData class is one of the center piece of this package, by managing data used for rendering a view template. 

The ViewData is the core of the responders which is used to transfer data between requests as well as from request to view's renderer. 

The ViewData manages following data, 

*	`ViewData::DATA`, set by with method.
* 	`ViewData::MESSAGE`, set by with{Message|AlertMsg|ErrorMsg} method. 
*  `ViewData::INPUTS`, set by withInputData method. 
*  `ViewData::ERRORS`, set by withInputErrors method. 

To retrieve the data, use `get` method as 

```php
$data = $viewData->get(ViewData::DATA);
```


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