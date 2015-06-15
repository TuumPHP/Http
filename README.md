Tuum/Respond
=========

Framework independent helpers and responders for PSR-7 Http/Message. 

> This package will bring extra functionality and ease of development to the simple middlewares and micro-frameworks, making them more suitable for ordinary web site development... 

Overview
--------

### Helper classes

Helper classes helps to manage Psr-7 http message objects. For instance, 

```php
$bool = ResponseHelper::isRedirect($response);
``` 

will evaluate $response to check for redirect response. 

### Responder classes

Responders to simplify the composing a response object, by providing various response types such as text, jason, html, or redirection. But more uniquely, the responders enables to transfer data across http requests using sessions and flashes. For instance, 

```php
// first request.
$response = Redirect::forge($request, $response)
    ->withMessage('welcome!') // <- message to the next view.
    ->toPath('jump/to');
ResponseHelper::emit($response);
exit;

// ...now in the subsequent request to a server...
Respond::forge($request, $response)
    ->asView('template'); // with the 'welcome!' message.
```

The message __"welcome!"__ set in the first request, will appear automatically in the second request. 

> looks familiar API? I like Laravel very much!

### License

MIT license

### Status

Alpha. 

### Packages

Currently, Tuum/Respond uses following packages: 

*   [Zendframework/Zend-Diactoros](https://github.com/zendframework/zend-diactoros),
*   [Aura/Session](),
*   [Container-interop/container-interop](https://github.com/auraphp/Aura.Session),
*   [Tuum/View](https://github.com/TuumPHP/View), and
*   [Tuum/Form](https://github.com/TuumPHP/Form).

> Yep, uses home grown views and forms (;´д｀)

### Services

Responders require several services to work properly; these services are defined mostly by these interfaces: 

*   ```ContainerInterface``` for containers, 
*   ```SessionStorageInterface``` for session,
*   ```ViewStreamInterface``` for views, and
*   ```ErrorViewInterface```.

> so, it is possible to use other packages by using adaptors, in theory. 

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

Responding basic text, json, or html. 

```php
use Tuum\Respond\Respond;
Respond::view($request)->asText('Hello World');
Respond::view($request)->asJson(['Hello' => 'World']);
Respond::view($request)->asHtml('<h1>Hello World</h1>');
Respond::view($request)->asDownload($fp, 'some.dat');
Respond::view($request)->asFileContents('tuum.pdf', 'application/pdf');
```

Responding to uri, path, base-path, or referrer.

```php
Respond::redirect($request)->toAbsoluteUri($request->getUri()->withPath('jump/to'));
Respond::redirect($request)->toPath('jump/to');
Respond::redirect($request)->toBasePath('to');
Respond::redirect($request)->toReferrer();
```

To use ```toBasePath```, set base-path to $request by ```RequestHelper::withBasePath($basePath);``` somewhere before.  


### Using Views (Template)

To view a template, you must provide a viewer that implements ```ViewStreamInterface```, something like,

```php
$app = new Container(); // must implement ContainerInterface
$app->set->(ViewStreamInterface::class new ViewStream());
RequestHelper::withApp($request, $app);
```

Once, that is done, you can view a template using ```asView``` method; 

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

### ContainerInterface

A service container provides a way to obtain services used by responders. 
Use ```RequestHelper::withApp``` method to set a container to $request. 

The container must implement ContainerInterface by Container-interop group. 

```php
$app = new Container(); // must implement ContainerInterface
$request = RequestHelper::withApp($request, $app);
```


### SessionStorageInterface

SessionStorageInterface provides ways to access session, which is taken 
primarily from Aura.Session's segment. 

To obtain a segment by Aura.Session, 

```php
use Aura\Session\SessionFactory;

$factory = new SessionFactory();
$session = $factory->newInstance($_COOKIES);
$segment = $session->getSegment('some-name');
```

Set the session storage to the container, or directly set to the $request.

```php
$app = new Container(); // must implement ContainerInterface
$app->set(SessionStorageInterface::class, $segment);
```

or 

```php
use Tuum\Respond\RequestHelper;
$request = RequestHelper::withSessionMgr($request, $segment);
```

> accurately speaking, the Aura's segment class does not implement the ```SessionStorageInterface```. so, do not set strict type hinting using the interface...

### ViewStreamInterface

The ```ViewStreamInterface``` extends Psr-7's ```StreamInterface``` to add extra methods for rendering a view/template. 

*   ```withView($view_name, ViewData $data)```: sets the template file name for the view and render data. 
*   ```withContent($view_name, ViewData $data)```: sets the contents of a view and render data. This method maybe used for rendering a static file. 

To respond using ```view``` resopnder, provide a ViewStream object implementing ```ViewStreamInterface``` interface. To use the default ```Tuum/View``` and ```Tuum/Form``` package, 

```php
$app = new Container(); // ContainerInterface
$view = ViewStream::forge(__DIR__ . '/view-dir');
$app->set(ViewStreamInterface::class, $view);
$request = RequestHelper::withApp($request, $app);
```

### ErrorViewInterface

The ```ErrorViewInterface``` is a simplified ViewStreamInterface which can render a view based on status code instead of view name as used in ViewStreamInterface. 

To respond using the ```error``` responder, provide an ```ErrorView``` object. To use the default set, 

```php
$app = new Container(); // ContainerInterface
$view = ViewStream::forge(__DIR__ . '/error-view-dir');
$error = new ErrorView($view);
$error->default_error = 'errors/error';
$error->statusView = [
    Error::FILE_NOT_FOUND => 'errors/notFound',
];
$app->set(ErrorViewInterface::class, $error);
RequestHelper::withApp($request, $app);
```

You can use the ```ErrorView``` object for PHP's ```set_exception_handler``` handle as well:

```php
set_exception_handler($error); // catch uncaught exception!!!
```

### ViewData

Turned out that this ViewData class is one of the center piece of this package, by managing data used for rendering a view template. 

It is the ```ViewStream```'s responsibility to correctly convert the information of ViewData object in the template renderer. 

> The ViewData is the core of the responders which is used to transfer data between requests as well as from request to view's renderer. 

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


