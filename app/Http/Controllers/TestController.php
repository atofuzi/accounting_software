<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Journal;
use App\Gentian;

class TestController extends Controller
{
    public function test(Request $request)
    {
        foreach ($request->items as $item) {
            foreach ($item as $key => $journal_data) {
                if (!empty(array_filter($journal_data))) {
                    $journal = new Journal;
                    $journal->user_id = $request->user_id;
                    $journal->account_date = $journal_data["account_date"];
                    $journal->account_subject_id = $journal_data["account_subject_id"];
                    $journal->summary = $journal_data["summary"];
                    $journal->amount = $journal_data["amount"];
                    $journal->journal_type = ($key === 'debit') ? 0 : 1; // debitの場合0（借方）,creditの場合1（貸方）
                    $journal->gentian_number = $journal_data['gentian_number'];
                    $journal->save();
                }
            }
        }

        $result = Journal::all();
        $json = json_encode($result, JSON_PRETTY_PRINT);
        return $result;
    }
}
