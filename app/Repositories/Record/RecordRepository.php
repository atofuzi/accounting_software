<?php

namespace App\Repositories\Record;

use App\Models\Journal;
use App\Models\CashAccountBook;
use App\Models\DepositAccountBook;
use App\Models\DepositBalance;
use App\Models\AccountsReceivableBook;
use App\Models\AccountsPayableBook;
use App\Models\AccountSubject;
use App\Models\ExpenseBook;
use App\Enums\AccountSubjects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

class RecordRepository implements RecordRepositoryInterface
{
    protected $journal;
    protected $cash;
    protected $deposit;
    protected $deposit_balance;
    protected $receivable;
    protected $payable;
    protected $expenses;

    /**
     * 
     * @param object $journal
     */
    public function __construct(
        Journal $journal,
        CashAccountBook $cash,
        DepositAccountBook $deposit,
        AccountsReceivableBook $receivable,
        AccountsPayableBook $payable,
        ExpenseBook $expenses,
        DepositBalance $deposit_balance
    ) {
        $this->journal = $journal;
        $this->cash = $cash;
        $this->deposit = $deposit;
        $this->receivable = $receivable;
        $this->payable = $payable;
        $this->expenses = $expenses;
        $this->deposit_balance = $deposit_balance;
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

    public function getCashRecord($params)
    {
        $user_id = Auth::id();
        // journalsテーブルから先月繰越残高を計算
        $last_balance = $this->journal->selectRaw('sum(debit_amount) - sum(credit_amount) as balance')
            ->where('user_id', $user_id)
            ->where('account_subject_id', AccountSubjects::CASH)
            ->where('account_date', '<', $params->date_from)
            ->first();

        $result['last_balance'] = (!empty($last_balance->balance)) ? $last_balance->balance : 0;

        $cash_records = $this->journal->select('journal_type', 'unit_number')
            ->where('user_id', $user_id)
            ->where('account_subject_id', AccountSubjects::CASH)
            ->where('account_date', '>=', $params->date_from)
            ->where('account_date', '<', $params->date_to)
            ->orderBy('account_date', 'asc')
            ->get();

        $result['items'] = [];
        foreach ($cash_records as $key => $cash_record) {
            $data = [
                ':user_id' => $user_id,
                ':unit_number' => $cash_record->unit_number,
                ':journal_type' => $cash_record->journal_type,
            ];

            $record_date = DB::select("SELECT j.id, account_date, unit_number,
                                                (CASE journal_type WHEN 0 THEN 1 WHEN 1 THEN 0 END) AS journal_type,
                                                summary, a.account_subject as target_account_subject, amount
                                            FROM journals as j INNER JOIN account_subjects as a  on j.account_subject_id = a.id 
                                            WHERE user_id = :user_id AND unit_number = :unit_number AND journal_type <> :journal_type;", $data);

            $data = json_decode(json_encode($record_date), true);
            $result['items'][$key] = $data[0];
        }
        return $result;
    }

    public function getDepositRecord($params)
    {
        $user_id = Auth::id();
        // [nits]銀行id,銀行名,預金種目でグループ化したレコードを取得
        $result = DB::select("SELECT 
                                distinct deposit_account_books.bank_id,
                                         banks.bank_name,
                                         account_subject_id,
                                         account_subject as deposit_kind
                                from deposit_account_books
                                    inner join banks on deposit_account_books.bank_id = banks.id
                                    inner join journals on deposit_account_books.journal_id = journals.id
                                    inner join account_subjects on journals.account_subject_id = account_subjects.id
                                where deposit_account_books.user_id = :user_id 
                                order by deposit_account_books.bank_id;", [':user_id' => $user_id]);

        $result = json_decode(json_encode($result), true);

        $column = [
            'journals.unit_number',
            'journals.journal_type',
        ];

        $result_column = [
            'journals.id',
            'journals.unit_number',
            'account_date',
            'summary',
            'account_subjects.account_subject as target_account_subject',
            'amount',
        ];

        foreach ($result as $key => $deposit) {
            $result[$key]['items'] = [];
            $result[$key]['last_balance'] = $this->getDepositBalance($user_id, $params, $deposit);

            $deposit_record_lists = $this->deposit
                ->select($column)
                ->join('journals', 'deposit_account_books.journal_id', '=', 'journals.id')
                ->where('journals.account_date', '>=', $params->date_from)
                ->where('journals.account_date', '<', $params->date_to)
                ->where('deposit_account_books.user_id', $user_id)
                ->where('deposit_account_books.bank_id', $deposit['bank_id'])
                ->where('journals.account_subject_id', $deposit['account_subject_id'])
                ->orderBy('journals.account_date', 'asc')
                ->get();

            foreach ($deposit_record_lists as  $deposit_record) {
                $target_account_type = ($deposit_record->journal_type === 0) ? 1 : 0;

                $items = $this->journal
                    ->select($result_column)
                    ->selectRaw("(CASE journal_type WHEN 0 THEN 1 WHEN 1 THEN 0 END) AS journal_type")
                    ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                    ->where('unit_number', $deposit_record->unit_number)
                    ->where('journal_type', $target_account_type)
                    ->get();
                foreach ($items as $item) {
                    array_push($result[$key]['items'], $item);
                }
            }
        }

        return $result;
    }
    public function getReceivableRecord($params)
    {
    }

    public function getPayableRecord($params)
    {
    }

    public function getExpensesRecord($params)
    {
        $user_id = Auth::id();
        $result = $this->journal->select('account_subject_id', 'account_subject as table_title')
            ->selectRaw('sum(debit_amount) - sum(credit_amount) as last_balance')
            ->join('expense_books', 'journals.id', '=', 'expense_books.journal_id')
            ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
            ->where('journals.user_id', $user_id)
            ->where('account_date', '<', $params->date_from)
            ->groupBy('account_subject_id')
            ->groupBy('account_subject')
            ->orderBy('account_subject_id', 'asc')
            ->get();
        
        if (!$result->isEmpty()) {
            foreach($result as $key => $result_item){
                $column = [
                    'id',
                    'account_date',
                    'summary',
                    'journal_type',
                    'amount',
                ];
                $result[$key]['items'] = $this->journal->select($column)
                                                        ->where('account_date', '>=', $params->date_from)
                                                        ->where('account_date', '<', $params->date_to)
                                                        ->where('user_id', $user_id)
                                                        ->where('account_subject_id', $result_item['account_subject_id'])
                                                        ->orderBy('account_date','asc')
                                                        ->get();
            }
        } else {
            $result = $this->journal->select('account_subject_id', 'account_subject as table_title')
                                        ->join('expense_books', 'journals.id', '=', 'expense_books.journal_id')
                                        ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                                        ->where('journals.user_id', $user_id)
                                        ->where('account_date', '>=', $params->date_from)
                                        ->where('account_date', '<', $params->date_to)
                                        ->groupBy('account_subject_id')
                                        ->groupBy('account_subject')
                                        ->orderBy('account_subject_id', 'asc')
                                        ->get();
            foreach ($result as $key => $result_item ) {
                $column = [
                    'id',
                    'account_date',
                    'summary',
                    'journal_type',
                    'amount',
                ];
                $result[$key]['items'] = $this->journal->select($column)
                                                        ->where('account_date', '>=', $params->date_from)
                                                        ->where('account_date', '<', $params->date_to)
                                                        ->where('user_id', $user_id)
                                                        ->where('account_subject_id', $result_item['account_subject_id'])
                                                        ->orderBy('account_date','asc')
                                                        ->get();
                $result[$key]['last_balance'] = 0; 
            }
        }

        return $result->toArray();
    }

    public function getTotalAccountRecord($params)
    {
    }

    /**預金出納帳の先月末残取得
     * 
     * @var array $params
     * @return array 
     */
    public function getDepositBalance($user_id, $params, $deposit)
    {
        $ymd = explode("-", $params['date_from']);
        $result = $this->deposit_balance
            ->selectRaw('sum(balance) as last_balance')
            ->where('user_id', $user_id)
            ->where('bank_id', $deposit['bank_id'])
            ->where('deposit_item_id', $deposit['account_subject_id'])
            ->where('account_month', '<', $ymd[0] . '-' . $ymd[1])
            ->first();

        return (!empty($result->last_balance)) ? $result->last_balance : 0;
    }
}
