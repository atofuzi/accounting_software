<?php

namespace App\Repositories\Record;

use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RecordRepository implements RecordRepositoryInterface
{
    protected $journal;
    /**
     * 
     * @param object $journal
     */
    public function __construct(Journal $journal)
    {
        $this->journal = $journal;
    }

    /**
     * 仕訳帳データ取得
     * 
     * @var $params
     * @return array
     */
    public function getJournalRecord($params)
    {
        $user_id = Auth::id();

        // 会計日・摘要（科目名+コメント）・元丁・仕訳タイプ・金額
        $column = [
            'journals.unit_number',
            'journals.account_date',
            'account_subjects.account_subject',
            'journals.summary',
            'gentians.gentian_number',
            'journals.journal_type',
            'journals.amount'
        ];

        $result = DB::table('journals')
            ->select($column)
            ->Join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
            ->Join('gentians', 'gentians.account_subject_id', '=', 'journals.account_subject_id')
            ->where('journals.user_id', '=', $user_id)
            ->where('journals.account_date', '>=', $params->date_from)
            ->where('journals.account_date', '<', $params->date_to)
            ->orderBy('account_date', 'asc')
            ->orderBy('journals.id', 'asc')
            ->get();

        return $result;
    }
}
