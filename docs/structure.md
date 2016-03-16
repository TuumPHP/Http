Directory Structure
===================

`Tuum/Respond` comes with 3 template renderer: Twig, PHP League's Plates, and own `Tuum/View`. 
In this section, twig is used as an example. 

Template Files
--------------

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

### Twig Viewer

To use default twig viewer, construct such as:

```php
$viewer = TwigViewer::forge(
    '/dir/to/twigs',                   // twig template file directory.
    [                                  // options for Twig_Environment.  
        'cache' => '/dir/to/cache',
        'auto_reload' => true,
    ], 
    function(Twig_Environment $twig) { // further configure $twig here...
        return $twig;
    };
```

### Contents Template

The `layouts/contents.twig` is used to render a generic html content 
inside a `layout`. The contents may look like, 

```html
{% extends "layouts/layout.twig" %}

{% block content %}

    {{ contents|raw }}

{% endblock %}
```

To use the contents, use `$responder` as;

```php
$responder->view($req, $res)->asContents('your html content');
```

### Error Files and Error Responder

The default location for error files are `/twigs/errors`. 

```
twigs/
 + errors/
   + error.twig
   + forbidden.twig
   + notFound.twig
```


The `error.twig` is the generic error template file displayed to errors not specified. 
Other error files, `notFound.twig` or `forbidden.twig` maybe created and used by error responder. 

```php
$error = ErrorView::forge(
    $view, [
        'default' => 'errors/error',
        'status'  => [
            404 => 'errors/notFound',
            403 => 'errors/forbidden',
        ],
    ]);
```


Building $responder
----

then, build `$responder` using the `$viewer` and `$error`, such as

```php
$responder = ResponderBuilder::withServices(
    $viewer, 
    $error,
    'layouts/contents',
    $app->getResolver()
)
```

view template as 

```php
$responder->view($req, $res)->render('index');
```

and view error file as 

```php
$responder->error($req, $res)->forbidden();
```

