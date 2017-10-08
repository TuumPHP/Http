Sample Code
============

Assume you have already built a responder (`$responder`).

Simple Cases
------------

### Rendering a Template

Rendering a template is easy. 
The sample code below is used to draw the top page of the 
demo site. 

```php
$app->get('/{name}', function($req, $res, $args) use($responder) {
	$name = $args['name'] ?? 'Tuum/Respond';
	return $responder->view($req, $res)
		->setSuccess('Welcome, ' . $name)
		->render('welcome');
});
```

### Redirect with Messages

It is also very easy to redirect with messages and form inputs saved in flash session to be retrieved in the subsequent request. 

```php
$app->post('/check', function($req, $res, $args) use($responder) {
	return $responder->redirect($req, $res)
		->setAlert('Redirected!')
		->toReferrer();
});
```

### Error

```php
$app->get('/form', function($req, $res, $args) use($responder) {
	return $responder->error($req, $res)
		->setError('Sorry!')
		->forbidden();
});
```

Redirect After Validation Errors
-------------------------------

This pattern is often used in Laravel application; 
you fill in a form and submit, there is some validation errors, 
and application redirect back to the form page 
with the validation errors and input values.  

The sample code below is in `JumpController` class that uses 
`DispatchByMethodTrait` helper traits to simplify responder API. 
The class is located at `/app/App/Controller/JumpController.php`

### Showing HTML Form

This is a sample code to show HTML form 
(the 'jump' page have the html form).


```php
/**
 * @return ResponseInterface
 */
protected function onGet()
{
    $this->responder
        ->getViewData()
        ->setSuccess('try jump to another URL. ')
        ->setData('jumped', 'text in control')
        ->setData('date', (new \DateTime('now'))->format('Y-m-d'));

    return $this->view()->render('jump');
}
```

### Redirect 

There is no validation, but redirect with always the 
same error and messages. 

```php
/**
 * @return ResponseInterface
 */
protected function onPost()
{
    return $this->redirect()
        ->setError('redirected back!')
        ->setInput($this->getPost())
        ->setInputErrors([
            'jumped' => 'redirected error message',
            'date'   => 'your date',
            'gender' => 'your gender',
            'movie'  => 'selected movie',
            'happy'  => 'be happy!'
        ])->toReferrer();
}
```

### Template

The messages and forms are automatically populated with 
input values. 

Ugh, rewrite the template!


