Sample Codes
============

This section dissects sample codes in this Demo. 
The code assumes that the `$responder` object of `Responder` class 
is already constructed in prior. 

The route and handler are defined in `/app/appRoute.php` file. 

Simple View
-----------

### Closure Callable

The root page shows a simple case of rendering a view template 
from a closure. 

```php
$app->add('/',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
    
        // set welcome message if visiting for the first time. 
        if (!$responder->session()->get('first.time')) {
            $responder->session()->set('first.time', true);
            $responder->getViewData()
                ->setSuccess('Thanks for downloading Tuum/Respond.');
        }
        return $responder
            ->view($request, $response)
            ->render('index');
    });
```

* `$responder->session()` returns `SessionStorageInterface` object 
to manage session variables, flash storage, as well as CSRF token. 
* `$responder->getViewData()` returns a `ViewDataInterface` data 
transfer object shared inside `Tuum/Respond` objects. The message 
is carried to View template, `index`. 
* `$resopnder->view(...)->render(...)` renders a `index` template 
and returns a view response. 

### View Template


Post-Redirect-Get Pattern
-------------------------

A sample site at `localhost:8888/jump` shows a Post-Redirect-Get (PRG) Pattern. 

```php
$app->add('/jump', \App\App\Controller\JumpController::class);
```

The `JumpController` 

#### Get a Form

The route callable simply renders `jump` template with default success message. 

```php
$app->add('/jump',
    function ($request, $response) use ($responder) {
        $responder
            ->getViewData()
            ->setSuccess('try jump to another URL. ');
        return $responder
            ->view($request, $response)
            ->render('jump');
    });
```

#### Post and Redirect

The page has a link to `/jumper` which is handled by the following callable.

```php
$app->add('/jumper',
    function ($request, $response) use ($responder) {

        // 1. set error messages etc. to $viewData.
        $responder->getViewData()
            ->setError('redirected back!')
            ->setInputData(['jumped' => 'redirected text'])
            ->setInputErrors(['jumped' => 'redirected error message']);

        // 2. redirect back to /jump with the viewData. 
        return $responder
            ->redirect($request, $response)
            ->toPath('jump');
    });
```

The `$viewData` data is saved as session's flash data, 
then retrieved in the subsequent request by `$responder->getViewData()` method. 


Using Presenter Callable
------------------------

Some complex page deserves a dedicated object to manage a view. 
`Tuum/Respond` provides a presenter object/callable that is dedicated to provide a view. 

A presenter class must implements `PresenterInterface`; as such.

```php
class PresentViewer implements PresenterInterface {
    /** @var Responder */
    private $responder;
    function __invoke($request, $response, $viewData) {
        return $this->responder
            ->withView($viewData)
            ->view($request, $response)
            ->render('some view', $viewData);
    }
}
```

Then, call the presenter, as: 

```php
return $responder->view($request, $response)->call(PresentViewer::class);
```

It is possible to call a presenter inside a template. 

