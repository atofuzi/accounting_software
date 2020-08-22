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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//　仕訳帳の入力・編集
Route::middleware('auth:api')->get('use_account_subjects', 'JournalBookController@getUseAccountSubjects');
Route::middleware('auth:api')->get('gentian_numbers', 'JournalBookController@getGentianNumbers');
Route::middleware('auth:api')->get('bank_lists', 'JournalBookController@getBankLists');
Route::middleware('auth:api')->get('supplier_lists', 'JournalBookController@getSupplierLists');
Route::middleware('auth:api')->post('/journal_register', 'JournalBookController@register');
Route::middleware('auth:api')->get('/test', 'TestController@test');


