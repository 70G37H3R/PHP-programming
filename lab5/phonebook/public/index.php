<?php

define('ROOTDIR', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
define('APPNAME', 'My Phonebook');

// Turn off error display in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once ROOTDIR . 'vendor/autoload.php';
require_once ROOTDIR . 'db.php';

session_start();

use \App\Router;

if (!ob_get_level()) {
    ob_start();
}

// Auth routes
Router::post('/logout', '\App\Controllers\Auth\LoginController@logout');
Router::get('/register', '\App\Controllers\Auth\RegisterController@showRegisterForm');
Router::post('/register', '\App\Controllers\Auth\RegisterController@register');
Router::get('/login', '\App\Controllers\Auth\LoginController@showLoginForm');
Router::post('/login', '\App\Controllers\Auth\LoginController@login');

// Contact routes
Router::get('/', '\App\Controllers\ContactsController@index');
Router::get('/home', '\App\Controllers\ContactsController@index');

// Error routes
Router::error('\App\Controllers\Controller@notFound');


// Add routes
Router::get('/contacts/add', '\App\Controllers\ContactsController@add');
Router::post('/contacts', '\App\Controllers\ContactsController@create');

// Edit routes
Router::get('/contacts/edit/(:num)', '\App\Controllers\ContactsController@edit');
Router::post('/contacts/(:num)', '\App\Controllers\ContactsController@update');


// Delete routes
Router::post('/contacts/delete/(:num)', '\App\Controllers\ContactsController@delete');

Router::dispatch();

ob_end_flush();


