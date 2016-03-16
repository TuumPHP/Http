Responders
==========

Also, there is `session` to help manage sessions.


Responders
----------

There are 3 types of responders, `view`, `redirect`, and `error`, each of which specialized
to compose a response object in its own way.


### View Responder

`View` responder creates a response with a view body, such as basic text, json, or html text.


```php
$responder->view($request)->asView('template/filename'); // renders a template file.
$responder->view($request)->asText('Hello World'); // returns text/plain.
$responder->view($request)->asJson(['Hello' => 'World']); // returns text/json.
$responder->view($request)->asHtml('<h1>Hello World</h1>'); // returns as text/html.
$responder->view($request)->asDownload($fp, 'some.dat'); // binary for download.
$responder->view($request)->asFileContents('tuum.pdf', 'application/pdf'); // reads the file and sends as mime type.
$responder->view($request)->asContent('<h1>My Content</h1>'); // renders the text inside a contents template file.
$responder->view($request)->call($presenter);
```

to use `asContent` method, specify a template file name for rendering a content as second argument of the constructor:

```php
new View($view, 'contet-file-name');
```

to use `call` method, the `$presenter` is;

*   an object implementing `ViewerInterface`, or
*   a callable with argument same as the `ViewerInterface::withView`.


### Redirect Responder

The `Redirect` responder creates redirect responce to uri, path, base-path, or referrer.

```php
$responder->redirect($request)->toAbsoluteUri(
$request->getUri()->withPath('jump/to')
);
$responder->redirect($request)->toPath('jump/to');
$responder->redirect($request)->toBasePath('to');
$responder->redirect($request)->toReferrer();
```

to add queries,

```php
$responder->redirect($request)
->withQuery('some=value')
->withQuery(['more'=>'array'])
->toPath('with/query');
```

### Error Responder

The `Error` responder renders a template file according to the http status code

```php
$responder->error($request)->forbidden();     // 403: access denied
$responder->error($request)->unauthorized();  // 401: unauthorized
$responder->error($request)->notFound();      // 404: file not found
$responder->error($request)->asView($status); // error $status
```

Other Objects
-------------

### Session Storage

The `SessionStorageInterface` object is not a responder but managed as part of Responder object.

```php
$responder->session($request)->set($key, $value);
$responder->session($request)->get($key);
$responder->session($request)->setFlash($key, $value);
$responder->session($request)->getFlash($key, $value);
$responder->session($request)->getFlashNext($key);
$responder->session($request)->validateToken($value);
$responder->session($request)->getToken();
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


You can access the responder, or each of resopnders as:

```php
$responder = Resopnd::getResponder($request);
$view      = Resopnd::view($request, $response);
$redirect  = Resopnd::redirect($request, $response);
$error     = Resopnd::error($request, $response);
$session   = Resopnd::session($request);
```

