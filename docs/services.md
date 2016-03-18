Services and Interfaces
========

`Tuum/Respond`'s responders depends on services, that are defined by 
interfaces. 

`ViewerInterface`
----

This simple interface defines objects that renders a view from a template file, `$viewFile`. 
Another important role of these viewers is to convert input `$viewData` to `ViewHelper` object, that is passed to template files. 


```php
interface ViewerInterface
{
    /**
     * renders $viewFile template file with $viewData data.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param string                  $viewFile
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewFile, $viewData);
}
```

The `ViewerInterface` is used by `View` and `ErrorView` (which is used by `Error`) responders. 

There are three implementations of the `ViewerInterface` available: 
 `TwigViewer` for using Twig template,
 `PlatesViewer` for using Plates template, and 
 `TuumViewer` for using plain a PHP file as template. 

### `TwigViewer`

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

$view = TwigViewer::forge(
    $root_dir, 
    $options, 
	function(Twig_Environment $twig) {
		// further configure $twig renderer...
		return $twig;
	});
```

The third closure is an optional argument. 

### `PlatesViewer`

Uses PHP league's Plates template engine. 


### `TuumViewer`

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


`PresenterInterface`
----

This interface signifies objects that can render a view from `$viewData` as an input. 

```php
interface PresenterInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $viewData);
}
```

To use the `PresenterInterface` objects from responder, 

```php
$responder->view($req, $res)->call($presenter);
```

Also, they can be called from inside a template

```html
<?php
/** @var ViewHelper $view */
$view->call($presenter);
?>
```


`ErrorViewInterface`
----

The `ErrorViewInterface` renders a view from status code, `$status`,

```php
interface ErrorViewInterface
{
    /**
     * renders $view_file with $data.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface       $response
     * @param int                     $status
     * @param mixed|ViewDataInterface $viewData
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $status, $viewData);
}
```

`ErrorView` class provides the interface.

```php
$errorView = ErrorView::forge(
    $viewer, // ViewerInterface object
    [
        'default' => 'default/error/file',
        'status'  => [
            '403' => 'errors/403',
        ],
        'files'   => [
            'errors/bad' => [501, 502, 503],
        ],
    ]
);
```

where `status` option specifies template files for each `status`, whereas 
`files` options specifies `status` for each template file. 

`SessionStorageInterface`
----

```SessionStorageInterface``` provides ways to access session and flash data storage, whose API is taken from `Aura.Session`'s Segment class. 

The default implementation uses the Aura.Session. 

```php
use Tuum\Respond\Service\SessionStorage;

$session = SessionStorage::forge('some-name');
$responder = $responder->withSession($session);

$response = $next($request, $response); // call next
$session->commit();
```


Helper Classes
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
