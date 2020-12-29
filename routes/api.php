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
Route::middleware('auth:api')->get('use_account_subjects', 'AccountSubjectsController@useAccountSubjects');
Route::middleware('auth:api')->get('use_bank_lists', 'BankController@useBankLists');
Route::middleware('auth:api')->get('use_supplier_lists', 'SupplierController@useSupplierLists');
Route::middleware('auth:api')->post('journal_register', 'JournalRegisterController@journalRegister');
Route::middleware('auth:api')->get('journal_edit', 'JournalRegisterController@journalEdit');
Route::middleware('auth:api')->post('journal_update', 'JournalRegisterController@journalUpdate');
Route::middleware('auth:api')->put('journal_delete', 'JournalRegisterController@journalDelete');

// 各帳票データ取得
Route::middleware('auth:api')->get('/record_journal', 'RecordController@recordJournal');
Route::middleware('auth:api')->get('/record_cash', 'RecordController@recordCash');
Route::middleware('auth:api')->get('/record_deposit', 'RecordController@recordDeposit');
Route::middleware('auth:api')->get('/record_receivable', 'RecordController@recordReceivable');
Route::middleware('auth:api')->get('/record_payable', 'RecordController@recordPayable');
Route::middleware('auth:api')->get('/record_expenses', 'RecordController@recordExpenses');
Route::middleware('auth:api')->get('/record_total_assets', 'RecordController@recordTotalAssets');
Route::middleware('auth:api')->get('/record_total_liabilities_capital', 'RecordController@recordTotalLiabilitiesAndCapital');
Route::middleware('auth:api')->get('/record_total_cost', 'RecordController@recordTotalCost');
Route::middleware('auth:api')->get('/record_total_earnings', 'RecordController@recordTotalEarnings');
//Route::middleware('auth:api')->get('/record_total_account', 'RecordController@recordTotalAccount');
Route::middleware('auth:api')->get('/test', 'TestController@test');
