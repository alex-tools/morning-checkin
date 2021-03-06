<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

Route::get('/', 'home@index');
Route::controller('auth');

/* Follows */
Route::resourceful('follows', array('create', 'destroy'));

/* Checkins */
Route::resourceful('checkins', array('create', 'update', 'destroy'));
Route::get('dashboard','checkins@new');

/* Users */
Route::get('users/(:any)', array('uses' => 'users@show', 'as' => 'user'));
Route::resourceful('users', array('index'));

/* Pads */
Route::resourceful('pads', array('index', 'show', 'create'));
Route::get('pads/(:num)/hide', array('uses' => 'pads@hide', 'as' => 'hide_pad'));




/*
|--------------------------------------------------------------------------
| Asset Definitions
|--------------------------------------------------------------------------
|
| Don't f'in ask me why they're in here. 
| http://jasonlewis.me/code/basset/docs#installation
|
*/

Bundle::start('basset');

if (Config::get('basset')) Basset\Config::extend(Config::get('basset'));

Basset::scripts('website', function($basset)
{
  $basset->add('pjax', 'jquery.pjax.js')
         ->add('sisyphus', 'sisyphus.min.js')
         ->add('app', 'app.js', 'pjax');
});

Basset::styles('website', function($basset)
{
  $basset->add('reset', 'reset.css')
         ->add('fullcalendar', 'fullcalendar.css')
         ->add('font', 'font.css')
         ->add('ui_custom', 'ui_custom.css')
         ->add('fancybox', 'fancybox.css')
         ->add('bootstrap', 'bootstrap.css')
         ->add('elfinder', 'elfinder.css')
         ->add('plugins', 'plugins.css')
         ->add('styles', 'styles.css');
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});


/* DEPLOY HOOK */
Route::post('githubpull', function()
{
  $res = `git pull`;
  $res .= `php artisan migrate --env=production`;
  $res .= `rm -rf bundles/basset/compiled`;
  $res .= `mkdir bundles/basset/compiled`;
  $res .= `php artisan compile_assets`;
  echo $res;
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Router::register('GET /', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('/');
});