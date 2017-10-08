Responders
================

Constructing a Responder
------------------------

Use `Tuum\Respond\Builder` to construct a responder.  

```php
use Tuum\Respond\Builder;
use Tuum\Respond\Responder;

$builder = new Builder('App-Name')  // 1. application name
    ->setRenderer(                  // 2. renderer
        Plates::forge(__DIR__ . '/plates')
    )->setErrorOption([             // 3. error options
        'default' => 'layout/error',
        'status' => [
            401 => 'errors/unauthorized',
        ],
    ])->setContainer($container)    // 4. container
;
$responder = new Responder($builder);
```

1. **application name**: a string to identify the application. Not really used for much. 
3. **renderer**: to render a template; `Twig`, `League/Plates`, or `Tuum/View` are available. Used in View and Error responders. 
4. **error options**: set error options for default error page, and other pages for each http status. Used in Error responder. 
2. **container**: used to retrieve presenter objects. must be `ContainerInterface` of PSR-11. ignore this if not using presenter objects. 

Not all the options are required. 


### Respond Class

A Respond class provides a statical proxy to get responder. 

```php
Respond::setResponder($respond);
// ...
// somewhere else in the code
Respond::getResponder()->view($req, $res)->render('rainbow');
```


### Responders

There are `view`, `error`, and `redirect` responders.
You can access to the responder with `$request` and `$response` objects,

```php
$view     = $responder->view($request, $response);
$redirect = $responder->redirect($request, $response);
$error    = $responder->error($request, $response);
```

There are also: 

* `session` to help manage sessions. 
* `ViewData` is the central payload of `Tuum/Respond` that are shared between responders and requests via session. 

```php
$session  = $responder->session();
$viewData = $responder->getViewData();
```



View Responder
--------------

View responder creates a response with a view body, such as basic text, json, or html text.

```php
$view = $responder->view($request, $response);

return $view->render('template', $viewData);  // renders a template file.
return $view->asText('Hello World');        // returns text/plain.
return $view->asJson(['Hello' => 'World']); // returns text/json.
return $view->asHtml('<h1>Hello</h1>');     // returns as text/html.
return $view->asDownload($fp, 'file_name.csv', true, 'text/csv');  // binary for download.
return $view->asFileContents('tuum.pdf', 'application/pdf'); // reads the file and sends as mime type.
return $view->asContent('<h1>My Content</h1>'); // renders the text inside a contents template file.
```

to use asContent method, specify a template file name for rendering a content.


### Contents in a Layout

asContents method renders a static HTML inside a template layout. 

```php
$view->content_view = 'layout/contents'; // this is the default. 
return $view->asContent('<h1>My Content</h1>');
```

The contents will be rendered using the contets_layout file, which specifies how the contents be rendered. 

```twig
{% extends "layouts/layout.twig" %}

{% block content %}
    {{ contents|raw }}
{% endblock %}
```


### Calling a Presenter Object

A Presenter object is a class implementing `Tuum\Responder\Interfaces\PresenterInterface`... which can be called by;

```php
return $responder
	->view($request, $response)
	->call(MyPresenter::class, [    // call a presenter
		'some' => 'data',
	]);
```

Set container when building a responder to retrieve the presenter object from the class or service name. 

It is also possible to call a presenter inside a template file. This maybe useful when a template layout needs some database calls. 


Redirect Responder
------------------

The Redirect responder creates redirect response to a uri.

```php
$redirect = $responder->redirect($request, $response);
$redirect->toAbsoluteUri($request->getUri()->withPath('jump/to'));
$redirect->toPath('jump/to');
```

to add queries,

```php
$redirect
    ->withQuery('some=value')
    ->withQuery(['or' => 'give', 'in'=>'array'])
    ->toPath('with/query');
```

* TODO: add toReferer and toBasePath methods. 

Error Responder
---------------

The Error responder renders a template file according to the http status code

```php
$error = $responder->error($request, $response);
$error->forbidden();     // 403: access denied
$error->unauthorized();  // 401: unauthorized
$error->notFound();      // 404: file not found
$error->asView($status, $viewData); // error $status
```

### ErrorFileView Class

The `Error` class uses `ErrorFileView` class to determine which error template to render from http status (i.e. error code). It is an object implementing `Tuum\Respond\Interfaces\ErrorFileInterface`. 

The default implementation can be configured as;

```php
$error->errorFile->default_error = 'errors/error';
$error->errorFile->statusView = [
    401 => 'errors/unauthorized',  // for login error.
    403 => 'errors/forbidden',     // for CSRF token error.
    404 => 'errors/notFound',      // for not found.
];
```

it searches for `statusView` using `$status`; if not defined, uses `default_error` file. 


ViewData
---------

ViewData is a data-transfer-object implementing `Tuum\Respond\Interfaces\ViewDataInterface` that is shared by many objects in Tuum/Respond. 

1. ViewData object is retrieved from session, or instantiated from scratch, 
2. updated via various responders (view, redirect, etc.), then
3. passed to template view, OR <br> saved to session to be retrieved in the subsequent request. 

* TODO: clone viewData before passing it to renderer?

### API methods

* The responders (View, Redirect, and Error) all have the same setter methods of the ViewData. 

#### data

```php
public function setData($key, $value = null);
public function getData();
```

#### message ans status. 

```php
// setting message with error status. 
public function setMessage($type, $message);
public function setSuccess($message);
public function setAlert($message);
public function setError($message = null);
public function setCritical($message = null);

// retrieving messages and finding error status. 
public function getMessages(); // [[$type, $message], ...]
public function hasError();
public function getErrorType();
```

#### input and validation result. 

```php
public function setInput(array $post);
public function setInputErrors(array $errors);

public function getInput();
public function getInputErrors();
```



