<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Journal;
use App\Gentian;
use App\User;
use App\AccountSubject;
use Illuminate\Database\Eloquent\Builder;

class TestController extends Controller
{
    public function test(Request $request)
    {
        // まずrequestをdepositとcreditに分ける
        foreach ($request->items as $journal_data) {
            if (!empty(array_filter($journal_data['debit']))) {
                $journal = new Journal;
                $journal->user_id = $request->user_id;
                $journal->account_date = $journal_data['debit']["account_date"];
                $journal->account_subject_id = $journal_data['debit']["account_subject_id"];
                $journal->summary = $journal_data['debit']["summary"];
                $journal->amount = $journal_data['debit']["amount"];
                $journal->journal_type = 0; // debitの場合0（借方)
                $journal->gentian_number = $journal_data['debit']['gentian_number'];
                $journal->save();
            }
        }

        foreach ($request->items as $journal_data) {
            if (!empty(array_filter($journal_data['credit']))) {
                $journal = new Journal;
                $journal->user_id = $request->user_id;
                $journal->account_date = $journal_data['credit']["account_date"];
                $journal->account_subject_id = $journal_data['credit']["account_subject_id"];
                $journal->summary = $journal_data['credit']["summary"];
                $journal->amount = $journal_data['credit']["amount"];
                $journal->journal_type = 1; // debitの場合0（借方)
                $journal->gentian_number = $journal_data['credit']['gentian_number'];
                $journal->save();
            }
        }


        $result = Journal::all();
        $json = json_encode($result, JSON_PRETTY_PRINT);
        return $result;
    }

    public function get()
    {
        // 欲しいのは、ユーザ名、会計科目名
        // 検索条件は、user_id　かつアカウントidが2のレコード

        // journalsテーブルのsubject_idに対応した会計科目名をjournalsから取得したデータに追加で格納する
        $data = Journal::addSelect(['account_subject' => AccountSubject::select('account_subject')
            ->whereColumn('id', 'journals.account_subject_id')->limit(1)])
            ->addSelect(['user_name' => User::select('name')
                ->whereColumn('id', 'journals.user_id')->limit(1)])->get();

        // 金額の合計を返す→決算データ作る時に使える
        $data1 = Journal::where('account_subject_id', 2)->sum('amount');

        // firstOrCreateで取得or生成が可能
        // nameでフライトを取得するか、存在しなければ作成する
        // updateOrCreateで更新or生成が可能

        $id = 3;

        $journal = Journal::where('account_subject_id', 2)->get();

        $fresh_journal = $journal->fresh();

        // 取得結果をそのままforeachでループ
        foreach (Journal::where('account_subject_id', 2)->cursor() as $data) {
        }

        // joinを試す
        $query = self::createSelect();
        $query->leftJoin("users", "journals.user_id", "=", "users.id");
        $query->leftJoin("account_subjects", "journals.account_subject_id", "=", "account_subjects.id");
        $ret = self::addQuery($id, $query)->first();

        dd($ret);
    }

    private static function createSelect()
    {
        $columns = [
            'users.name',
            'users.email',
            'journals.account_date',
            'journals.account_subject_id',
            'account_subjects.account_subject',
            'journals.summary',
            'journals.amount',
        ];

        return DB::table("journals")
            ->select($columns)
            ->selectRaw('concat(\' (\',account_subjects.account_subject, \') \', journals.summary) AS subject_summary');
    }

    private static function addQuery($id, $query)
    {
        $query->where("journals.id", "=", $id);

        return $query;
    }
}
