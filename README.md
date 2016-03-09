Tuum/Respond
=========

`Tuum/Respond` is a __View layer__ (as in MVC2 architecture) for various PSR-7 based micro-frameworks, 
such as [Slim 3](http://www.slimframework.com) and [Zend-Expressive](https://zendframework.github.io/zend-expressive/).

It provides various methods for composing a response object, to help developing a __traditional web site__ 
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
$ git checkout 1.x
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

and for development, 

*   [Zendframework/Zend-Diactoros](https://github.com/zendframework/zend-diactoros) as a default PSR-7 objects,
*   [League/Plates](httphttp://platesphp.com/),
*   [Twig](http://twig.sensiolabs.org/), and maybe
*   [Tuum/View](https://github.com/TuumPHP/View).


Overview
--------

### Sample Construction

Following shows an example code for building a `$responder` object:

```php
use Tuum\Respond\Service\TwigView;
use Tuum\Respond\Helper\ResponderBuilder;

$responder = ResponderBuilder::withView(
    TwigView::forge('/dir/twig')
);
```

### Simple View

To render a template file (using some virtual `$app`): 

```php
$app->add('/',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
    
        // 1. create $viewData and set success message, and
        $viewData = $responder->getViewData()->setSuccess('Welcome!');
    
        // 2. render `index` template with the data. 
        return $responder->view($request, $response)
            ->render('index', $viewData);
    });
```



### Post-Redirect-Get Pattern

A sample site at `localhost:8888/jump` shows a Post-Redirect-Get (PRG) Pattern. 

The route callable simply renders `jump` template with default success message. 

```php
$app->add('/jump',
    function ($request, $response) use ($responder) {
        $viewData = $responder->getViewData()
            ->setSuccess('try jump to another URL. ');
        return $responder->view($request, $response)
            ->render('jump', $viewData);
    });
```

The page has a link to `/jumper` which is handled by the following callable.

```php
$app->add('/jumper',
    function ($request, $response) use ($responder) {

        // 1. set error messages etc. to $viewData.
        $viewData = $responder->getViewData()
            ->setError('redirected back!')
            ->setInputData(['jumped' => 'redirected text'])
            ->setInputErrors(['jumped' => 'redirected error message']);

        // 2. redirect back to /jump with the viewData. 
        return $responder->redirect($request, $response)
            ->toPath('jump', $viewData);
    });
```

The `$viewData` data is saved as session's flash data, 
then retrieved in the subsequent request by `$responder->getViewData()` method. 


### Using Presenter Callable

Some complex page deserves a dedicated object to manage a view. 
`Tuum/Respond` provides a presenter object/callable that is dedicated to provide a view. 

A presenter class must implements `PresenterInterface`; as such.

```php
class PresentViewer implements PresenterInterface {
    /** @var Responder */
    private $responder;
    function __invoke($request, $response, $viewData) {
        return $this->responder->view($request, $response)
        ->render('some view', $viewData);
    }
}
```

Then, call the presenter, as: 

```php
return $responder->view($request, $response)->call(PresentViewer::class);
```

It is possible to call a presenter inside a template. 


Templates and Form Helpers
----

Template files must understand the given `$viewData` information for `Tuum/Respond` to work properly

### Template Structure

Sample application for Twig template has following files. 

```
twigs/
 + errors/
   + error.twig
   + notFound.twig
 + layouts/
   + contents.twig
   + layout.twig
 + index.twig
 + jump.twig
 + upload.twig
```

### Error Files and Error Responder

The default location for error files are `app/twigs/errors`. 

The `error.twig` is the generic error template file displayed to errors not specified. Other error files, `notFound.twig` or `forbidden.twig` maybe created and used by error responder. 

```php
$error = ErrorView::forge($view, [
    'default' => 'errors/error',
    'status'  => [
        404 => 'errors/notFound',
        403 => 'errors/forbidden',
    ],
]);
```

where `$view` is the `ViewerInterface` object to render a template file. The `status` is a hashed array from error status code to template file. 


### Form Helpers

`Tuum/Respond` uses `Tuum/Form` as a view helper to interpret the view data. The helpers are located as `{{ viewData }}` in Twig template, as in the example code below. 

```html
{% extends "layouts/layout.twig" %}

{% block content %}

    <h1>Let's Jump!!</h1>

    {{ viewData.message.onlyOne|raw }}

    <p>This sample shows how to create a form input and shows the error message from the redirection.</p>

    <h3>Sample Form</h3>

    <div style="margin-left: 2em;">

        <form method="post" action="jumper" class="">

            {% if viewData.errors.exists('jumped') %}
            <div class="form-group has-error">
            {% else %}
            <div class="form-group ">
            {% endif %}
                {{ viewData.forms.label('some text:', 'jumped')|raw }}
                {{ viewData.forms.text('jumped', 'original text').id().class('form-control')|raw }}
                {{ viewData.errors.p('jumped')|raw }}
            </div>

            <input type="submit" value="jump!" class="btn btn-primary"/>&nbsp;
            <input type="button" value="clear" onclick="location.href='jump'" class="btn btn-default"/>

        </form>

    </div>

{% endblock %}
```

### Tuum/Form

There are following helpers which corresponds each of ViewData's data;

*   **inputs**: for ViewData's `inputData`,
*   **errors**: for ViewData's `inputErrors`,
*   **message**: for ViewData's `message` (i.e. `success`, `alert`, and `error`),
*   **data**: for ViewData's `data`,
*   **forms**: for displaying html form elements using ViewData's `inputData`,

### content file

The `asContent` method will render any text content within a template layout if `$content_file` is set. The content file **must have a section named "contents"**, which may look like, 

```php
{% extends "layouts/layout.twig" %}

{% block content %}

    {{ contents|raw }}

{% endblock %}
```

which must have the `contents`. 




Responders
---------

There are 3 types of responders, `view`, `redirect`, and `error`, each of which specialized to compose a response object in its own way. 

Also, there is `session` to help manage sessions.

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

### ViewData/ViewDataInterface

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
$viewData->getData(); // getter for setData.
$viewData->getInputData(); // getter for setInputData.
$viewData->getInputErrors(); // getter for setInputErrors.
$viewData->getMessage(); // getter for set{Message|Success|Alert|Error}. 
$viewData->getViewFile(); // getter for setViewFile. 
$viewData->getStatus(); // getter for setStatus. 
```


Respond Class
----

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

Constructing Responders
------------------

### Constructing a Responder Object

`Responder` object takes 3 responders, ViewData instances at the construction; then set SessionStorageInterface object.

```php
$responder = (new Responder(
	new View, 
	new Redirect, 
	new Error)
)->withSession($session);
```

#### `ResponderBuilder` class

`ResponderBuilder` class offers a simple static factory methods to construct the `$responder` object. 

A factory method, `ResponderBuilder::withView`, takes 3 arguments: `ViewerInterface` object, option for `ErrorView` class, and template file name for content view. 

```php
$responder = Responder::withView(
	TwigViewer::forge(__DIR__ . '/twigs'), 
	[
	    'default' => 'errors/error',
	    'status'  => [
	        404 => 'errors/notFound',
	]],
   'layouts/contents');
```

Another factory method, `ResponderBuilder::withServices`, also takes 3 arguments: `ViewerInterface` object, `ErrorViewInterface` object, and template file name for content view. 

```php
$view    = TwigViewer::forge(__DIR__ . '/twigs');
$error   = ErrorView::forge(
	TwigViewer::forge(__DIR__.'/errors'), [
	    'default' => 'errors/error',
	    'status'  => [
	        404 => 'errors/notFound',
	    ],
	]);
$content_file = 'layouts/contents';
$responder = Responder::withServices($view, $error, $content_file)
    ->withSession(SessionStorage::forge('sample'));
```

#### setting `$session` object

The responders need `SessionStorageInterfae` object in order to pass data from one request to another. The responder takes the session object using `withSession` method, not in constructor. 

```php
$responder = $responder->withSession($session);
```


#### setting `$response` object

Responders takes `$request` and `$response` objects as arguments. To omit the second `$response` object, set it using `withResponse` method in prior to using it. 

```php
$responder = $responder->withResponse($response);
return $responder->view($request)->asView('index');
```

> Responder needs a `$response` object to return since it does not know how to construct a response object. (as it being a framework agnostic module).


### `View` Responder

The `View` Responder takes `ViewerInterface` object, and optionally the template filename for content rendering. 

```php
$view = new Tuum\Respond\Responder\View(
    TwigViewer::forge($twig_root),
    'layout/contents'
);
```

### `Redirect` Responder

construction of Redirect responder does not take any arguments; 

```php
$redirect = new Tuum\Respond\Responder\Redirect();
```

### `Error` Responder

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

#### `TwigViewer`

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

#### `TuumViewer`

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

#### `ErrorView` object

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



### `SessionStorageInterface`

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

*   `withBasePath` / `getBasePath` / `getPathInfo`: manage a base path. pathInfo is the remaining of the entire path minus the base path. 
*   `withReferrer` / `getReferrer`: getReferer returns a path set by withReferrer, or `$_SEVER['HTTP_REFERER']`. 


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

### Referrer

manages referrer. 
