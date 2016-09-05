BreadCrumbs
===========

`Tuum/Form` provides a helper class for constructing Breadcrumbs. 

Overview
--------

### Creating BreadCrumb Object

In a controller (or in a template page), create a `BreadCrum` object. 

```php
use Tuum\Form\Components\BreadCrumb;
$bread = BreadCrum::forge('Bread Crumb');
```

Then, in the subsequent template pages, add upper level crumbs, as such.

```php
if (isset($bread)) {
    $bread->add('Document', '/docs/');
}
```

The first argument, `'Document'` is the label 
and the second `'/docs/` is the url link for the 
breadcrumb. 

### Output BreadCrumb HTML

Maybe at the top template layout page, you can write:  

```php
<?php if (isset($bread)): ?>
<ol class="breadcrumb">
    <?php foreach ($bread as $url => $label): ?>
        <?php
        if ($bread->isLast()) {
            echo "<li class='active'>{$label}</li>";
        } else {
            echo "<li><a href='{$url}' >{$label}</a></li>";
        }
        ?>
    <?php endforeach; ?>
</ol>
<?php endif; ?>
```

The example above shows a html/css for Bootstrap 3. 

### From Route-Callable

It is possible to pass the breadcrumb object from a 
route-callable closure. 

```php
function (ServerRequestInterface $request, $response) {
    return Respond::view($request, $response)
        ->asContents('<h1>Contents</h1><p>this is a string content in a layout file</p>', [
            'bread' => BreadCrumb::forge('Contents')->add('Samples', '#'),
        ]);
});
```
