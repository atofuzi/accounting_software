<?php 

namespace App\Repositories\JournalBook;

use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class JournalBookRepository implements JournalBookRepositoryInterface
{
    protected $journalBook;
    /**
     * 
     * @param object $journalBook
     */
    public function __construct(Journal $journalBook)
    {
        $this->journalBook = $journalBook;
    }

    /**
     * 仕訳帳データ取得
     * 
     * @var $params
     * @return array
     */
    public function getList($params)
    {
        $userId = Auth::id();
        // 会計日・摘要（科目名+コメント）・元丁・仕訳タイプ・金額
        $column = [
            'journals.account_date',
            'account_subjects.account_subject',
            'journals.summary',
            'gentians.gentian_number',
            'journals.journal_type',
            'journals.amount'
        ];

        $query = DB::table('journals')
                        ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                        ->join('gentians','journals.account_subject_id', '=', 'gentians.account_subject_id')
                        ->where('user_id', $userId)
                        ->select($column)
                        ->get();

        return ($query);

    } 
}