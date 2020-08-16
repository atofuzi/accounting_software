<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    return view('index');
});

Auth::routes();

Route::get('/home', 'JournalController@index')->name('journal')->middleware('auth');
Route::post('/journal', 'JournalController@register')->name('journal')->middleware('auth');
Route::put('/home', 'JournalController@update')->name('journal')->middleware('auth');

Route::get('/test', 'TestController@get');
