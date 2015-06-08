Tuum/Http
=========

Helpers and Responders for PSR-7 Http/Message. 

Overview
--------

__Helper classes__ offer static methods for managing psr-7 http message objects. 
For instance, 

```php
ResponseHelper::isRedirect($response);
``` 

will evaluate $response to check for redirect response. 

__Responder classes__ offer a simplified way to create a response object, such 
as text, jason, html, or redirection. But more uniquely, the responders are 
designed to pass data across http requests using sessions and flashes, by 
properly configuring $request. 


### License

MIT licnese

### Status

Alpha. 

### Packages

Currently, following packages are used as primarily source. 

*   Zend/Diactoros
*   Container-interop/container-interop

and for require-dev, 

*   Aura/Session


Responders
----------

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

To use ```toReferrer```, set referrer to $request by ```RequestHelper::withReferrer($referrer);``` somewhere before. 


### Using Views (Template)

To view a template, code like, 

```php
use Tuum\Http\Respond;
Respond::forge($request)
    ->with('name', 'value)
    ->withMessage('message')
    ->withAlert('alert-message')
    ->withError('error-message')
    ->asView('view-file');
```

To use ```asView```, set ViewStreamInterface in $app container.

```php
$app = new Container;
$app->set(ViewStreamInterface::class, $viewer);
RequestHelper::withApp($request, $app);
```


### Configuration

To use all the functions in the responders, session and views must be configured properly. 

Primarily Aura.Session is used as session service. 

```php
$request = new Psr\Http\Message\
```
Currently uses Tuum\Http\Service\SessionStorageInterface


Helpers
-------
