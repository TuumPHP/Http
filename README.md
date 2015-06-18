Tuum/Respond
=========

Tuum/Respond is a framework independent helpers and responders for PSR-7 Http/Message. 

*   Still an Alpha Release. 
*   PSR-1, PSR-2, and PSR-4.

### License

MIT license

### Installation and samples

Use git to install Tuum/Responder from github. 

```sh
git clone https://github.com/TuumPHP/Respond
```

to see a sample site, use PHP's internal server at public folder as;

```sh
$ cd Respond/public
$ php -S localhost:8888 index.php
```

and access ```localhost:8888``` by any browser. The sample site uses external bootstrap css and javascript. 


Overview
--------

Tuum/Respond can be used together with Psr-7 based middlewares and micro-frameworks to compliment extra functionalities for developing ordinary web sites. 

### Helper classes

Helper classes helps to manage Psr-7 http message objects. For instance, 

```php
$bool = ResponseHelper::isRedirect($response);
``` 

will evaluate $response to check for redirect response. There are helpers for request and responce. 

### Responder classes

The __responders__ are the main part of this package.

Responders to simplify the composing a response object, by providing various response types such as text, jason, html, or redirection. But more uniquely, the responders enables to transfer data across http requests using sessions and flashes. For instance, 

```php
$app = new App(); // some micro-framework app. 

// redirects to /jumped.
$app->get('/jumper', function($request) {
	return Respond::redirect($request, $response)
	    ->withMessage('bad input!') // <- set up info.
	    ->withInputData(['some' => 'value'])
	    ->withInputErrors(['some' => 'bad value'])
	    ->toPath('jump/to');
	});

// ...and this is jumped.
$app->get('/jumped', function($request) {
	return Respond::view($request, $response)
	    ->asView('template'); // with the 'welcome!' message.
});
```
Accessing ```/jumper``` will redirect to ```/jumped``` with the message __"welcome!"__ and other data. These data appears automatically in the second request's view. 

> looks familiar API? I like Laravel very much!

### Services

The Psr-7 http/message does not provide all the necessary functionalities to use the responders. You need to supply services that are defined by following interfaces. 

*   ```ContainerInterface``` for containers, 
*   ```SessionStorageInterface``` for session,
*   ```ViewStreamInterface``` for views, and
*   ```ErrorViewInterface```.


### Packages

Currently, Tuum/Respond uses following packages: 

*   [Zendframework/Zend-Diactoros](https://github.com/zendframework/zend-diactoros),
*   [Aura/Session](),
*   [Container-interop/container-interop](https://github.com/auraphp/Aura.Session),
*   [Tuum/View](https://github.com/TuumPHP/View), and
*   [Tuum/Form](https://github.com/TuumPHP/Form).

> Yep, uses home grown views and forms (;´д｀)


Responders Overview
-------------------

The respnders simplfies the composition of response object by using various services and information from ```$request```. There are 3 responders: 

*   ```Respond::view```: to create a response with a view body. 
*   ```Respond::redirect```: to create a redirect response. 
*   ```Respond::error```: to create response with error status and view. 

To start responders, provide ```$request``` and ```$response``` objects to the methods above. If ```$response``` is not given, the responders will create a new ```$response``` object. 

```php
use Tuum\Respond\Respond;
Respond::view($request, $response);
// or...
Respond::view($request);
```

For the simplicity of the code, the subsequent samples use only ```$request``` as input. 


### Basic Usage

```view``` responder creates basic text, json, or html responces. 

```php
use Tuum\Respond\Respond;
Respond::view($request)->asText('Hello World');
Respond::view($request)->asJson(['Hello' => 'World']);
Respond::view($request)->asHtml('<h1>Hello World</h1>');
Respond::view($request)->asDownload($fp, 'some.dat');
Respond::view($request)->asFileContents('tuum.pdf', 'application/pdf');
```

```redirect``` responder creates redirect responce to uri, path, base-path, or referrer.

```php
Respond::redirect($request)->toAbsoluteUri($request->getUri()->withPath('jump/to'));
Respond::redirect($request)->toPath('jump/to');
Respond::redirect($request)->toBasePath('to');
Respond::redirect($request)->toReferrer();
```


### Using Views (Template)

```view``` responder can create responce using view (or template). 

```php
Respond::view($request)
    ->with('name', 'value')
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->asView('view-file');
```

Similarly, ```asContent``` method will render any text content within a template layout if ```ViewStream``` object implements it. 

```php
Respond::view($request)
    ->asContent('<h1>My Content</h1>');
```

To use Views feature, provide ```ViewStreamInterface``` object to the responders. 

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

### Error Responder

Error responder generates a template view based on the status code by using ```ErrorViewInterface``` object. Set up the error view, then,

```php
Respond::error($request)->forbidden();
```

To enable this feature, provide ```ErrorViewInterface``` object to the responders. 

Services
-------------

The responders requires many services to operate. 
These services are stored in ```$request->withAttribute``` method. 

### SessionStorageInterface

```SessionStorageInterface``` provides ways to access session and flash data storage, whose API is taken primarily from Aura.Session's segment. 

To obtain a segment by Aura.Session, 

```php
use Aura\Session\SessionFactory;

$factory = new SessionFactory();
$session = $factory->newInstance($_COOKIES);
$segment = $session->getSegment('some-name');
```

Set the session storage in the $request object using RequestHelper, as.

```php
$request = RequestHelper::withSessionMgr($request, $segment);
```

> strictly speaking, the Aura's segment class does not implement the ```SessionStorageInterface```. so, do not set strict type hinting using the interface...


### ViewStreamInterface

The ```ViewStreamInterface``` extends Psr-7's ```StreamInterface``` to add extra methods for rendering a view/template. 

*   ```withView($view_name, ViewData $data)```: sets the template file name for the view and render data. 
*   ```withContent($view_name, ViewData $data)```: sets the contents of a view and render data. This method maybe used for rendering a static file. 

To respond using ```view``` resopnder, provide a ViewStream object implementing ```ViewStreamInterface``` interface. To use the default ```Tuum/View``` and ```Tuum/Form``` package, 

```php
use Tuum\Respond\Service\ViewStream;
use Tuum\Respond\Service\ViewStreamInterface;

$request->withAttribute(ViewStreamInterface::class, ViewStream::forge(__DIR__.'/views'));
```

### ErrorViewInterface

The ```ErrorViewInterface``` is a simplified ViewStreamInterface which can render a view based on status code instead of view name as used in ViewStreamInterface. 

To respond using the ```error``` responder, provide an ```ErrorView``` object. To use the default set, 

```php
$error = new ErrorView(ViewStream::forge(__DIR__ . '/error-view-dir'));
$error->default_error = 'errors/error';
$error->statusView = [
    Error::FILE_NOT_FOUND => 'errors/notFound',
];$request->withAttribute(ErrorViewInterface::class, $error);
```

You can use the ```ErrorView``` object for PHP's ```set_exception_handler``` handle as well:

```php
set_exception_handler($error); // catch uncaught exception!!!
```

### ContainerInterface

A container maybe used to provide the services to responders. The container must implement ```ContainerInterface``` by Container-interop group. 

Populate the container with the services using interface class name as a key, and set it in the $request as;

```php
$app = new Container(); // some ContainerInterface...
$app->set(SessionStorageInterface::class, $session);
$app->set(ViewStreamInterface::class, $views);
$app->set(ErrorViewInterface::class, $error);
$request = RequestHelper::withApp($request, $app);
```


Helpers Overview
----------------

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
==================

To use a view/template system, it has to satisfy the followings:

*   implement ViewStreamInterface,
*   understand ViewData object.

Currently, only the default ViewStream uses Tuum/View and Tuum/Form matches these criteria. 

### ViewData

Turned out that this ViewData class is one of the center piece of this package, by managing data used for rendering a view template. 

The ViewData is the core of the responders which is used to transfer data between requests as well as from request to view's renderer. 

### Tuum/Form

Tuum/Form can interpret ViewData's withInputData and withInputErrors and converts to $inputs and $errors objects; then injects $inputs into the $form, a form generator. 

```php
<?= $forms->text('jumped', $data->jumped)->id()->class('form-control'); ?>
<?= $errors->get('jumped'); ?>
```

The $form will check for $inputs, then the specified value ($data->jumped value) to populate the value for the form. 
