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
Route::post('payment', 'ProductController@payment');
Route::get('/foo', function () {
    Artisan::call('storage:link');
});
Route::get('storage/{filename}', function ($filename)
{
    $path = storage_path('public/storage/files/products/' . $filename);

    if (!File::exists($path)) {
        return 'oi';
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
Route::get('category', 'CategoryController@index');
Route::middleware('jwt.verify')->group(function(){
    Route::delete('product/{id}', 'ProductController@destroy');
    Route::post('product', 'ProductController@create');
    Route::post('product/{id}', 'ProductController@update');
    Route::get('product/{id}', 'ProductController@show');
    Route::post('cart/{id}', 'ProductController@cart');
    Route::post('cart/checkout/order', 'ProductController@checkoutCart');
    Route::get('cart', 'ProductController@getCart');
    Route::get('order/{id}', 'ProductController@orderbyid');
    Route::get('cart/{id}/checkout', 'ProductController@checkout');
    Route::delete('cart/{id}', 'ProductController@deleteCart');
    Route::get('order', 'ProductController@order');
    Route::get('order/seller/list', 'ProductController@orderSeller');

});
Route::get('me', 'UserController@getAuthenticatedUser');
