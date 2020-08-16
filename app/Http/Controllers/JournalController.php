<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use App\UseAccountsSubject;

class JournalController extends Controller
{
    public function index()
    {
        return view('journal');
    }
    public function register(Request $request)
    {
        dd($request);
        $account_subject_id = $request->debit_account_subject_id;
        $account_subject = AccountSubjectTable::get($account_subject_id);
        
        //DB::table('sessions')->insert([
            //'amount' => $request->debit_amount,
            //'account_month' => 7,
            //'account_subject_id' => $account_subject_id,
            //'account_date' => $request->account_date,
            //'gentian_number' => $request->debit_gentian_number, 

        //]);

        return view('journal');

    }
    public function getUseAccountSubject($userId){
        $column = [
            'account_subject_id',
            'account_subjects.account_subject'
        ];
        $useAccountSubjects = DB::table('use_account_subjects')
                                ->select($column)
                                ->join('account_subjects', 'account_subject_id', 'account_subjects.id')
                                ->where('user_id',$userId)
                                ->get();
        return $useAccountSubjects;
    }
}


class AccountSubjectTable
{
    public static $account_subjects = [
        '現金' => 1,
        '普通預金' => 2,
        '売掛金' => 3,
        '事業主貸' => 4,
        '買掛金' => 5,
        '借入金' => 6,
        '未払費用' => 7,
        '預かり金' => 8,
        '事業主借' => 9,
        '元入金' => 10,
        '売上' => 11,
        '経費' => 12,
        '雑収入' => 13,
    ];
    
    public static function get($id){
        $account_subjects = self::$account_subjects;
        $account_subject = array_search($id,$account_subjects);
        return $account_subject;
    }
}