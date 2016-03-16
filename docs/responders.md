Responders
==========

There are `view`, `error`, and `redirect` responders. 

```php
$responder = new Responder($view, $redirect, $error);
```

You can access to the responder with `$request` and `$response` object, 

```php
$view     = $responder->view($request, $response);
$redirect = $responder->redirect($request, $response);
$error    = $responder->error($request, $response);
```

There is also `session` to help manage sessions. 

#### construction helpers

Since each of the responder also is dependent on other object (called services) 
and options, there is a `ResponderBuilder` class to help construct a Responder. 


View, Error, and Redirect
----------

### View Responder

`View` responder creates a response with a view body, such as basic text, json, or html text.


```php
$view = $responder->view($request, $response);
$view->render('template', $viewData);  // renders a template file.
$view->call($presenter, $viewData);    // call a presenter, object or class name. 
```

to use `call` method, the `$presenter` must be;

*   an object implementing `PresenterInterface`, or
*   a class name implementing PresenterInterface if resolver is given.


There are some useful method to return a response. 

```php
$view = $responder->view($request, $response);
$view->asText('Hello World');        // returns text/plain.
$view->asJson(['Hello' => 'World']); // returns text/json.
$view->asHtml('<h1>Hello</h1>');     // returns as text/html.
$view->asDownload($fp, 'some.dat');  // binary for download.
$view->asFileContents('tuum.pdf', 'application/pdf'); // reads the file and sends as mime type.
$view->asContent('<h1>My Content</h1>'); // renders the text inside a contents template file.
```

* to use `asContent` method, specify a template file name for rendering a content. 


#### construction

`View`'s constructor takes 3 arguments: ViewerInterface object, content file name, and a resolver. 

```php
new View(
    $viewer, 
    'content-file',
    $resolver);
```

* `$viewer` is an object implementing `ViewerInterface`.
* 'content-file' is a template to render content.
* `$resolver` is a invokable object to instantiate a class name (i.e some container). 


### Error Responder

The `Error` responder renders a template file according to the http status code

```php
$error = $responder->error($request, $response);
$error->forbidden();     // 403: access denied
$error->unauthorized();  // 401: unauthorized
$error->notFound();      // 404: file not found
$error->asView($status, $viewData); // error $status
```

#### construction

```php
$error = new Error($errorView);
```

where `$errorView` is an object implementing `ErrorViewInterface`. 


#### add new method

You should be able to add new error method,

```php
$error->methodStatus[
    'teaPot'  => 418,
    'illegal' => 451,
];
```

### Redirect Responder

The `Redirect` responder creates redirect response to uri, path, base-path, or referrer.

```php
$redirect = $responder->redirect($request, $response);
$redirect->toAbsoluteUri($request->getUri()->withPath('jump/to'));
$redirect->toPath('jump/to');
$redirect->toBasePath('to');
$redirect->toReferrer();
```

to add queries,

```php
$redirect
    ->withQuery('some=value')
    ->withQuery(['more'=>'array'])
    ->toPath('with/query');
```

#### construction

`Redirect` does not have any dependency. 

```php
$redirect = new Redirect();
```



Other Objects
-------------

### Session Storage

The `SessionStorageInterface` object is not a responder but managed as part of Responder object.

```php
$responder->session()->set($key, $value);
$responder->session()->get($key);
$responder->session()->setFlash($key, $value);
$responder->session()->getFlash($key, $value);
$responder->session()->getFlashNext($key);
$responder->session()->validateToken($value);
$responder->session()->getToken();
```

> `Redirect` responder uses the session to store $viewData in flash using `setFlash` method, and 
> retrieved in `getViewData` method. 

Set `session` in `$responder` using `withSession` method.  

```php
$responder = $responder->withSession(SessionStorage::forge('app'));
```


### Respond Class

`Respond` class offers an easy way to manage the responder object. Please set the `$responder` object in `$request` object as:

```php
// set $responder object in a middleware or somewhere.
$request = Respond::withResponder($request, $responder);
```

The `$responder` object is set as an attribute of the `$request` object, and accessible anywhere using `Respond`'s static method.

```php
$app->get('/jump', function($request, $response) {
    return Respond::view($request, $response)
        ->asView('jump');
});
```

You can access the responder, or each of responders as:

```php
$responder = Resopnd::getResponder($request);
$view      = Resopnd::view($request, $response);
$redirect  = Resopnd::redirect($request, $response);
$error     = Resopnd::error($request, $response);
$session   = Resopnd::session($request);
```

### Response Object

It is possible, but not recommended, to set `$response` object in `$responder` 
to omit when calling responders. 

```php
$responder = $responder->withResponse($response);
return $responder->redirect($request)->back();
```

The `$response` is an immutable object; the response object maybe an old one 
when used in later in the code. 