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

Route::get('/', function () {
    return view('welcome');
});


Route::post('/api/register', 'UserController@register')->middleware('cors');
Route::post('/api/login', 'UserController@login')->middleware('cors');

Route::group(['middleware' => 'cors'], function (){
    Route::resource('/api/cars', 'CarController');
});
