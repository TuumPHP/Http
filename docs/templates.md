ViewData and Helpers
====================

The `ViewData` processed in controller or presenter is passed to template as `ViewHelper` object, 
which is a proxy to various view helpers to construct html view. For instance, 

```php
$viewData = $responder->getViewData();
$viewData->setSuccess('Hello World');
```

can be viewed as message in a bootstrap div box by the ViewHelper.


For pure PHP template systems, such as `Plates` and `Tuum/View`, 
the `ViewHelper` is just a `$view`;

```php
<?= $view->message; ?>
```

For `Twig`, the ViewData is passed as `view`.

```html
{{ view.message | raw }}
```

Data Helper
----

For storing general data, such as entity. 
For example, following data maybe passed to template. 

```php
$viewData->setData('key', 'value');
$viewData->setData('div', '<div>this is a div</div>');
$viewData->setData('list', [
    ['id' => 1, 'lang' => 'PHP'],
    ['id' => 2, 'lang' => 'node.js'],
]);
```


### Twig

Since Twig comes with strong escape capability, the data are 
passed as the Twig's data as is. Som access data, such as, 

```html
{{ key }}
{{ div | raw }}
{% for item in list %}
  {{ item.id }}: {{ item.lang }}
{% endfor %}
```

### Plates and Tuum/View

In PHP based templates, the data are passed to Data Helper 
(Tuum\Forms\Data\Data class). Then, access as `$view->data`. 

```php
<?= $view->data->key;        // escaped ?>
<?= $view->data->get('key'); // escaped ?>
<?= $view->data->raw('div'); // as is   ?>
<?php
$list = $view->data->extractKey('list');
foreach($list as $item):
?>
  * <?= $item->id ?>: <?= $item->lang; ?>
<?php endforeach; ?>
```

#### Raw Data

For PHP based template systems, the data are packed in a `data` helper. 
Use `setRaw` to pass data as raw data. 

```php
$viewData->setRawData('raw', 'raw value');
```

then, in a template, access `raw` as,

```php
<?= $raw; ?>
```

InputData and InputErrors Helpers
-------------------------

The `inputData` and `inputErrors` are used primary to pass form input values 
and validation errors back to the input form. 

```php
$viewData = $responder->getViewData()
    ->setInputData($request->getParsedBody())
    ->setInputErrors([
        'jumped' => 'redirected error message',
    ]);
```

### Errors 

Errors helper shows the value in the InputErrors,

```php
$view->errors->jumped;                 // escaped
$view->errors->get('jumped', $value); // returns error if exists. or the $value.
$view->errors->p('jumped');            // outputs in bootstrap's text-danger class. 
```

The `exists` method provides a handy tool to construct some html based on 
error condition. 

```php
$view->errors->exists('jumped') ? 'has-error' : ''; // returns boolean if error exists.  
```

### Inputs 

Access to the original form input values by `inputs` helper. 
Use `get` method with `key` and default value, such as,

```php
<input type="text" name="jumped" value="<?= $view->inputs->get('jumped', $jumped) ?>" class="form-control" />
```

or, in Twig,

```html
<input type="text" name="jumped" value="{{ view.inputs.get('jumped', jumped) }}" class="form-control" />
```


Message Helper
-------

As many messages with 3 types, `success`, `alert`, and `error`, can be set in `ViewData`. 

```php
$viewData->setSuccess('Hello World');
$viewData->setAlert('That is interesting');
$viewData->setAlert('Help Me!');
```

The `message` helper can show all the messages as

```php
echo $view->message;
```

or, only one most severe message, as,

```php
echo $view->message->onlyOne();
```


Sample Templates
---------

Following shows a sample Twig code to construct a form element for bootstrap using the `ViewHelper`.

```html
<form method="post" action="jumper">
    <div class="form-group{% if view.errors.exists('jumped') %} has-error{% endif %}">
        <label for="jumped" >
            some date:
            <input type="text" id="jumped" name="jumped" 
                value="{{ view.inputs.get('jumped', jump) }}" class="form-control" />
        </label>
        {{ view.errors.p('jumped')|raw }}
    </div>
</form>
```

The first if statement sets div with 'has-error' class if error exists for 'jumped' value. 

```html
<div class="form-group{% if view.errors.exists('jumped') %} has-error{% endif %}">
```

The value of the input element is set to `jumped` value in `ViewData` set in a controller or 
presenter, but can be overwritten by `inputsData` if it exists. 

```html
<input type="text" id="jumped" name="jumped" 
    value="{{ view.inputs.get('jumped', jumped) }}" class="form-control" />
```

The last statement shows errors in `<p class="text-danger">...</p>` if error exists. 

```html
{{ view.errors.p('jumped')|raw }}
```

