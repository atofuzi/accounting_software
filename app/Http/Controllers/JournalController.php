<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index()
    {
        return view('journal');

    }
    public function create(Request $request)
    {

        
        $data = $request->account_date;
        (int)$account_subject_id = $request->debit_account_subject_id;

        DB::insert('insert into sessions (account_month, account_date, account_subject_id, gentian_number, amount) values (7,2020-07-08,1,20,10000)');

        session(['account_month' => 7]);
        session(['account_date' => $data ]);
        session(['account_subject_id ' => $account_subject_id ]);
        session(['summary' => $request->debit_summary ]);
        session(['gentian_number' => $request->debit_gentian_number]);
        session(['bank_id' => 1]);
        session(['amount' => $request->debit_amount]);
        session(['ledgers_id' => 1 ]);
        session(['journal_flg' => 0]);
        session(['created_at' => Carbon::now() ]);
        session(['updated_at' => Carbon::now() ]);



        return view('journal');

    }
}