<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['domain' => 'daddyjoon.com'], function(){
    Route::get('email/verify')->name('verification.verify');
});Route::get('/{any}', function ($any) {  return Redirect::to('https://daafyapp.com');})->where('any', '.*');
