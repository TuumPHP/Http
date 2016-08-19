Sample Codes
============

Simple Code
-----------

### Construction

Following shows an example code for building a `$responder` object:

```php
use Tuum\Respond\Service\Renderer\Twig;
use Tuum\Respond\Helper\ResponderBuilder;

$responder = ResponderBuilder::withView(
    Twig::forge('/template/dir/twig')
);
```

### Simple View

To render a template file (using some virtual `$app`): 

```php
$app->add('/',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($responder) {
    
        // 1. create $viewData and set success message, and
        $responder = $responder->withView(function(ViewDataInterface $view) {
            $view->setSuccess('Welcome!');
        });
    
        // 2. render `index` template with the data. 
        return $responder->view($request, $response)
            ->render('index');
    });
```


Post-Redirect-Get Pattern
-------------------------

A sample site at `localhost:8888/jump` shows a Post-Redirect-Get (PRG) Pattern. 

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

