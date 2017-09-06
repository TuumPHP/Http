Renderer and Template
==================

ViewHelper
----------

ViewHelper helps to render a template using the ViewData contents. 

Please reference Tuum/Form components for more details of these objects. 


### Message

To render a message, 

```php
echo (string) $viewHelper->message(); 
```

will render an html:

```html
<div class="alert alert-{$type}">{$message}</div>
```

To change the html, set: 

```php
$viewHelper->message()->formats = [
	'error' => '<div class="alert alert-error">%s</div>',
	....
]; 
```

### Data

```php
$data = $viewHelper->data();
echo (string) $data['some']; // returns escaped value
echo $data->hiddenTag('some'); // shows hidden tag with name 'some' and associated value. 
foreach($data as $key => $value) { // $value is escaped.
	
}
```

### Inputs

returns an original input values (i.e. post data).

```php
$inputs = $viewHelper->inputs();
echo $inputs->get('user[name]'); // returns 
echo $inputs->checked('user[isOK]', 'OK'); // returns ' checked' if user['isOK'] is 'OK', or an array containing 'OK'. 
```

### Input Errors

```php
$errors = $viewHelper->errors();
echo $errors->p('user[name]'); // returns ' checked' if user['isOK'] is 'OK', or an array containing 'OK'. 
```

will output, 

```html
<p class="text-danger">%s</p>
```

and to change the default html, 

```php
$viewHelper->errors()->format = '<p class="text-danger">%s</p>'; 
```

### Request Related

```php
$viewHelper->request(); // returns $request object
$viewHelper->uri();     // returns $request->getUri() object
$viewHelper->attribute('_token'); // returns $request's attribute
```

### Other Methods

renders another template with the same `$viewHelper` and additional data. 

```php
$viewHelper->render('template_name', ['more'=>'data']);
```

renders a presenter class with the same `$viewHelper` and additional data. 

```php
$viewHelper->call(SomePresenter::class, ['more'=>'data']);
```


Renderers
---------

The renderers are classes implementing `Tuum\Respond\Interfaces\RendererInterface` that has only one methods: 

```php
interface RendererInterface
{
    /**
     * @param string     $template
     * @param ViewHelper $helper
     * @param array      $data
     * @return string
     */
    public function render($template, ViewHelper $helper, array $data = []);
}
```

There are `Twig`, `League/Plates`, and `RawPhp` adapters are already defined. 

### Twig

Full forge options: 

```php
use Tuum\Respond\Service\Renderer\Twig;

$renderer = Twig::forge(
	'twig_template_dir', [
		'cache' => 'cache_dir',
		...
	], 
	function(Twig_Environment $twig) {
		// do something on $twig
});
```


When rendering a twig template, the ViewHelper is set as global variables, as such,

```php
    $this->renderer->addGlobal($this->viewName, $helper);
```

`$viewName` is a public property to be modified when the name 'view' is already in use. 

### League/Plates

Full forge options: 

```php
use League\Plates\Engine;
use Tuum\Respond\Service\Renderer\Plates;

$renderer = Plates::forge(
	'twig_template_dir', 
	function(Engine $plates) {
		// do something on $plates
});
```

When rendering a plates template, the ViewHelper is set as one of the data, as such,

```php
public function render($template, ViewHelper $helper, array $data = [])
{
    $this->renderer->addData(['view' => $helper]);
    return $this->renderer->render($template, $data);
}
```

`$viewName` is a public property to be modified when the name 'view' is already in use. 



