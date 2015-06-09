Tuum/Http
=========

Helpers and Responders for PSR-7 Http/Message. 

Overview
--------

__Helper classes__ helps to manage psr-7 http message objects. 
For instance, 

```php
$bool = ResponseHelper::isRedirect($response);
``` 

will evaluate $response to check for redirect response. 

__Responder classes__ offer a simplified way to create a response object, such 
as text, jason, html, or redirection. But more uniquely, the responders can pass data across http requests using sessions and flashes. For instance, 

```php
Redirect::forge($request)->withMessage('welcome!')->toPath('jump/to');

// ...now in the subsequent request to a server...
Respond::forge($request)->asView('template'); // with the 'welcome!' message.
```

### License

MIT license

### Status

Alpha. 

### Packages

Currently, following packages are used as primarily source. 

*   Zend/Diactoros
*   Container-interop/container-interop

and for require-dev, 

*   Aura/Session

### Services

Responders require several services to work properly, that are defined by (mostly) 
 these interfaces: 

*   ```ContainerInterface``` (by Container-interop), 
*   ```SessionStorageInterface```,
*   ```ViewStreamInterface```, and
*   ```ErrorViewInterface```,

and additionally, the viewers must understand this class. 

*   ```ViewData```.


Responders Overview
-------------------

The respnders are helpers to simplify the construction of a response object by using various information from ```$request```. There are 3 responders: 

*   ```Respond```: to create a response with a view body. 
*   ```Redirect```: to create a redirect response. 
*   ```Error```: to create response with error status and view. 



### Basic Usage

Responding basic text, json, or html. 

```php
use Tuum\Http\Respond;
Respond::forge($request)->asText('Hello World');
Respond::forge($request)->asJson(['Hello' => 'World']);
Respond::forge($request)->asHtml('<h1>Hello World</h1>');
```

Responding to uri, path, base-path, or referrer.

```php
use Tuum\Http\Redirect;
Redirect::forge($request)->toAbsoluteUri($request->getUri()->withPath('jump/to'));
Redirect::forge($request)->toPath('jump/to');
Redirect::forge($request)->toBasePath('to');
Redirect::forge($request)->toReferrer();
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
Tuum\Http\Respond::forge($request)
    ->with('name', 'value')
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->asView('view-file');
```

Similarly, ```asContent``` method will render any text content within a template layout if ```ViewStream``` object implements it. 

```php
Tuum\Http\Respond::forge($request)
    ->asContent('<h1>My Content</h1>');
```


### Passing Data From Redirect To View

Use Redirect and Respond responders to pass data between requests as, 

```php
Redirect::forge($request)
    ->with('extra', 'value')
    ->withInputData(['some' => 'value'])
    ->withInputErrors(['some' => 'error message'])
    ->withError('error-message')
    ->toPath('back/to');
```

then, receive the data as,

```php
Respond::forge($request)
    ->asView('some-view'); // all the data from the previous request.
```

The data set by using ```with``` methods will be stored in a session's flash data; the subsequent ```Respond::forge method``` will automatically retrieve the flash data and populate them in the template view. 

To enable this feature, provide ```SessionStorageInterface``` object to the responders.  

### Error Responder

Error responder generates a template view based on the status code by using ```ErrorViewInterface``` object. Set up the error view, then,

```php
Error::forge($request)->forbidden();
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
use Tuum\Http\RequestHelper;
$request = RequestHelper::withSessionMgr($request, $segment);
```

Currently uses Tuum\Http\Service\SessionStorageInterface

### ViewStreamInterface

```ViewStreamInterface``` extends a ```StreamInterface``` with extra methods for rendering a template. 

*   ```withView($view_name, ViewData $data)```: sets the template file name for the view and render data. 
*   ```withContent($view_name, ViewData $data)```: sets the contents of a view and render data. This method maybe used for rendering a static file. 

to use the ```asView``` and ```asContent``` method in ```Respond``` responders, you must set a ```ViewStreamInterface``` object in a container, as such. 


### ViewData

Turned out that this ViewData class is one of the center piece of this package, by managing data used for rendering a view template. 

It is the ```ViewStream```'s responsibility to correctly convert the information of ViewData object in the template renderer. 


### ErrorViewInterface

Provide the ErrorView object to the responder using a container. 

```php
$app = new Container(); // must implement ContainerInterface
$app->set->(ErrorViewInterface::class new ErrorView());
RequestHelper::withApp($request, $app);
```

You can use the ```ErrorView``` object for PHP's ```set_exception_handler``` handle.
