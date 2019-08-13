<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PageController@index')->name('index');
Route::post('/', 'PageController@processForm')->name('processForm');

Route::get('/clear', 'PageController@clear');

Route::get('/home', 'PageController@home')->name('home');

Auth::routes();

Route::get('/admin', 'PageController@admin')->name('admin');
Route::post('/admin', 'PageController@formAdmin')->name('formAdmin');

Route::get('/admin/register', 'UserController@showRegistrationForm')->name('showRegistrationForm');
Route::post('/admin/register', 'UserController@register')->name('register');
