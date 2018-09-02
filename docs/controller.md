Controller Helpers
==================

There are several traits available to help construct a controller class. 

DispatchByMethodTrait
--------

This trait helps to dispatch internal method, such as onGet based on the request method. 

```php
// route for MyCOntroller class. 
$app->get('/user/{name}', MyController:class);

// route handler 
class MyController
{
    use DispatchByMethodTrait;
    
    /**
     * inject responder at construction.
     * @param Responder $responder
     */
    public function __construct($responder) {
        $this->setResponder($responder);
    }
    /**
     * Assuming this is the entry point for controller. 
     */
    public function __invole($req, $res) {
        // ↓dispatch based on method. 
        return $this->dispatch($req, $res);
    }
    /**
     * receive route 
     */
    public function onGet($name) {
        $this->view()->render('name', [
            'name' => $name,
        ])
    }
}
```

The action method must have a name: `on{Method}`, such as 
`onGet`, `onPost`, and `onDelete`. 

### Arguments

The variable name in the action methods are matched with the query parameter. 
For the above case, `$_GET['name']` parameter is passed to `$name`. 


### Internal Methods


Method names must be `on` + `method name`.  Calling responders is simpler (no arguments) than calling responders itself. 

```php
protected function getRequest(); // get request
protected function getUploadFiles(); // get uploaded files in PSR-7
protected function getPost($name = null); // get post inputs (PSR-7's parsedBody())
protected function view(); // get view responder
protected function redirect(); // get redirect responder
protected function error(); // get error responder
protected function session(); // get sessionStorageInterface
protected function getPayload(); // get viewDataInterface
protected function call($presenter, array $data = []); // invoke presenter
```

Presenter
---------

A presenter is a class implementing `Tuum\Respond\Interfaces\PresenterInterface`;

```php
interface PresenterInterface
{
    /**
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response, 
        ViewDataInterface $viewData);
}
```

### Calling a Presenter

To call presenter class, 

```php
$response = $responder->view($request, $response)->call($presenter);
```

The `$presenter` must be a callable or an object implementing the `PresenterInterface`, or a string or class name to be retrieved from a container. 
Set a container to responder builder to retrieve a presenter from the container. 

### Calling a Presenter In a Template

The view helper has a `call` method to call a presenter; 

```php
$contents = $viewHelper->call($presenter);
```

which will return a string to be rendered inside the template. 

### PresenterTrait

There is a `PresenterTrait` to help construct a presenter. 

### PresentByContentTrait

PresentByContentTrait helps to negotiate the content-type request. 

One of the methods, `html`, `json`, or `xml`, is invoked based on the 
`accept` header of the request. 

```php
class MyPresenter implements PresenterInterface
{
    use PresentByContentTrait;
    
    public function html() {}
    public function json() {}
    public function xml() {}
}
```

to use your content type;

```php
class MyPresenter implements PresenterInterface
{
    protected $methodList = [
        'content-type' => 'methodName',
    ];
}
```
