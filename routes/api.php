<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');
Route::get('product', 'ProductController@index');
Route::middleware('jwt.verify')->group(function(){
    Route::post('cart/{id}', 'ProductController@cart');
    Route::get('cart', 'ProductController@getCart');
    Route::delete('cart', 'ProductController@delete');
});
Route::get('me', 'UserController@getAuthenticatedUser');
