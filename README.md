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
as text, jason, html, or redirection. 

But more uniquely, the responders can pass data across http requests 
using sessions and flashes, by properly configuring $request. 


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

*   ContainerInterface (by Container-interop), 
*   SessionStorageInterface,
*   ViewStreamInterface, and
*   ErrorViewInterface,

and additionally, the viewers must understand this class. 

*   Value.


Responders Overview
-------------------

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

To view a template, code like, 

```php
Tuum\Http\Respond::forge($request)
    ->with('name', 'value')
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->asView('view-file');
```

To use ```asView```, set ViewStreamInterface in $app container. 

### Passing Data From Redirect To View

when redirecting, 

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
$extra = RequestHelper::getFlash($request, 'extra');
Respond::forge($request)
    ->asView('some-view');
```

The inputData, inputErrors, and with{Message|Alert|Error} are automatically 
populated as view's data. 

The ```with``` method will keep the value in flash data but have retrieve it 
manually with ```RequestHelper::getFlash``` method.


Helpers Overview
----------------


Configuration
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

ViewStreamInterface is a stream which renders a template. 

```php
$app = new Container();
$app->set(ViewStreamInterface::class, $viewer);
RequestHelper::withApp($request, $app);
```

