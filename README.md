# J Router
A simple, lightweight, easy-to-use complete routing library in PHP.
## Installation

### .htaccess
```apacheconf
RewriteEngine On
# Don't rewrite files or directories
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
# Rewrite everything else to index.php
RewriteRule ^ index.php [L]
```
### index.php
```PHP
require('Jrouter.php');
Route::get('/home',function(){
    include('home.php');
});

Route::get('/profile','profile.php');
```
## Methods
### get
```PHP
Route::get('/path',function(){
    #codes
});
```
### post
```PHP
Route::post('/path',function(){
    #codes
});
```
### put
```PHP
Route::put('/path',function(){
    #codes
});
```
### delete
```PHP
Route::delete('/path',function(){
    #codes
});
```
### any
For any type of request method
```PHP
Route::any('/path',function(){
    #codes
});
```
### for

For multiple type of request method
```PHP
Route::for(['post','get'],'/path',function(){
    #codes
});
```

### * Defaut
```PHP
Route::any('*',function(){
    echo '404';
});
```

## Dynamic url

```PHP
Route::any('/user/{name}/{id}',function($params_){
    $name=$params_['name'];
    $id=$params_['id'];
    //or
    $name=Route::$params['name'];
    $id=Route::$params['id'];
});
```
## Send files to client
```PHP
Route::any('/user/{name}/{id}','user.php');
```
In `user.php` you can write.
```PHP
<h1>Hello, <?php echo Route::$params['name'];?></h1>
<p> Your id is  <?php echo Route::$params['id'];?></p>

```
## Access global variables
### Using g()
```php
$r=1;
Route::any('/path',function(){
    echo g('r');
});
```
### Using use
```php
$r=1;
Route::any('/path',function() use ($r){
    echo $r;
});
```
## Query Params
Instead of using `$_GET['param']` or `$_POST['param']` or to access `PUT` and `DELETE` parameters you can use
```php
Route::$queryParams['param'];
```
