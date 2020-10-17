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
        $column = [
            'deposit_account_books.bank_id as id',
            'banks.bank_name',
            'account_subject_id',
            'account_subject as deposit_kind',
        ];
        $last_month_deposits = $this->deposit->select($column)
                                                    ->selectRaw('sum(debit_amount) - sum(credit_amount) as last_balance')
                                                    ->join('banks', 'deposit_account_books.bank_id', '=', 'banks.id')
                                                    ->join('journals', 'deposit_account_books.journal_id', '=', 'journals.id')
                                                    ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                                                    ->where('deposit_account_books.user_id', $user_id)
                                                    ->where('journals.account_date', '<', $params->date_from)
                                                    ->groupBy('deposit_account_books.bank_id')
                                                    ->groupBy('banks.bank_name')
                                                    ->groupBy('account_subject_id')
                                                    ->groupBy('account_subject')
                                                    ->orderBy('id')
                                                    ->get();

        $current_month_deposits = $this->deposit->select($column)
                                                    ->selectRaw('0 as last_balance')
                                                    ->join('banks', 'deposit_account_books.bank_id', '=', 'banks.id')
                                                    ->join('journals', 'deposit_account_books.journal_id', '=', 'journals.id')
                                                    ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                                                    ->where('deposit_account_books.user_id', $user_id)
                                                    ->where('journals.account_date', '>=', $params->date_from)
                                                    ->where('journals.account_date', '<', $params->date_to)
                                                    ->groupBy('deposit_account_books.bank_id')
                                                    ->groupBy('banks.bank_name')
                                                    ->groupBy('account_subject_id')
                                                    ->groupBy('account_subject')
                                                    ->orderBy('id')
                                                    ->get();

        // 取得データを比較し、パターン判定
        $case = $this->get_record_case($last_month_deposits, $current_month_deposits);
        // 返却データ格納用配列
        $result = [];

        switch($case){
            case 1: // 両方とも無し
                return $result;
                break;
            case 2: // 先月まで無し、今月有り
                //return "// 先月まで無し、今月有り";
                foreach ($current_month_deposits as $key => $deposit) {
                    $result[$key]['bank_name'] = $deposit['bank_name'];
                    $result[$key]['deposit_kind'] = $deposit['deposit_kind'];
                    $result[$key]['last_balance'] = $deposit['last_balance'];
                    $result[$key]['items'] = $this->getDepositRecordItems($user_id, $params, $deposit);
                }
                break;
            case 3: // 先月まで有り、今月無し
                foreach ($last_month_deposits as $key => $deposit) {
                    $result[$key]['bank_name'] = $deposit['bank_name'];
                    $result[$key]['deposit_kind'] = $deposit['deposit_kind'];
                    $result[$key]['last_balance'] = $deposit['last_balance'];
                    $result[$key]['items'] = [];
                }
                break;
            case 4: // 先月まで有り、今月も有り
                $new_deposits = $this->array_map(['id','account_subject_id'], $current_month_deposits);
                $old_deposits = $this->array_map(['id','account_subject_id'], $last_month_deposits);
            
                // 先月までのデータに今月のデータを結合する
                $deposits = array_values($old_deposits + $new_deposits);
                foreach ($deposits as $key => $deposit) {
                    $result[$key]['bank_name'] = $deposit['bank_name'];
                    $result[$key]['deposit_kind'] = $deposit['deposit_kind'];
                    $result[$key]['last_balance'] = $deposit['last_balance'];
                    $result[$key]['items'] = $this->getDepositRecordItems($user_id, $params, $deposit);
                }
                break;
        }
        return $result;
    }
    
    public function getReceivableRecord($params)
    {
        // 1.先月データ無し：今月無し
        //   →空でデータを返す（条件分岐のelse）
        // 2.先月データ無し：今月有り
        //   →今月のデータのみを取得し、先月残高は0を入れればいい
        // 3.先月データ有り：今月無し
        //   →先月有りの取引先idで先月の残高と今月のデータを取得すればいい
        // 4.先月データ有り：今月有り
        //   →先月までと今月のリストを比較し、差分のみを先月に結合し、当月の会計データを取得する

        // 取得パターンの判定
        // 先月までの取引先一覧と今月の取引先一覧を比較
        //  1.両方とも無し
        //  2.先月無し、今月有り
        //  3.先月有り、今月無し
        //  4.先月有り、今月あり

        $user_id = Auth::id();

        // 先月の取引先id,取引先名,残高情報を取得
        $last_month_suppliers = $this->receivable->select('supplier_id as id', 'suppliers.supplier_name')
            ->selectRaw('sum(debit_amount) - sum(credit_amount) as last_balance')
            ->join('journals', 'accounts_receivable_books.journal_id', '=', 'journals.id')
            ->join('suppliers', 'accounts_receivable_books.supplier_id', '=', 'suppliers.id')
            ->where('accounts_receivable_books.user_id', $user_id)
            ->where('journals.account_date', '<', $params->date_from)
            ->groupBy('supplier_id')
            ->groupBy('suppliers.supplier_name')
            ->orderBy('id')
            ->get();

        // 今月の取引先id,取引先名を取得
        $current_month_suppliers = $this->receivable->select('supplier_id as id', 'suppliers.supplier_name')
            ->selectRaw('0 as last_balance')
            ->join('journals', 'accounts_receivable_books.journal_id', '=', 'journals.id')
            ->join('suppliers', 'accounts_receivable_books.supplier_id', '=', 'suppliers.id')
            ->where('accounts_receivable_books.user_id', $user_id)
            ->where('journals.account_date', '>=', $params->date_from)
            ->where('journals.account_date', '<', $params->date_to)
            ->orderBy('id')
            ->distinct()
            ->get();

        // 取得データを比較し、パターン判定
        $case = $this->get_record_case($last_month_suppliers, $current_month_suppliers);
        // 返却データ格納用配列
        $result = [];

        switch($case){
            case 1: // 両方とも無し
                return $result;
                break;
            case 2: // 先月まで無し、今月有り
                //return "// 先月まで無し、今月有り";
                foreach ($current_month_suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = $this->getReceivableRecordItems($user_id, $params, $supplier);
                }
                break;
            case 3: // 先月まで有り、今月無し
                foreach ($last_month_suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = [];
                }
                break;
            case 4: // 先月まで有り、今月も有り、中身が不一致
                $new_suppliers = $this->array_map(['id'], $current_month_suppliers);
                $old_suppliers = $this->array_map(['id'], $last_month_suppliers);
                // 先月までのデータに今月のデータを結合する
                $suppliers = array_values($old_suppliers + $new_suppliers);

                foreach ($suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = $this->getReceivableRecordItems($user_id, $params, $supplier);
                }
                break;
        }
        return $result;
    }
    public function getPayableRecord($params)
    {
        $user_id = Auth::id();

        // 先月の取引先id,取引先名,残高情報を取得
        $last_month_suppliers = $this->payable->select('supplier_id as id', 'suppliers.supplier_name')
            ->selectRaw('sum(debit_amount) - sum(credit_amount) as last_balance')
            ->join('journals', 'accounts_payable_books.journal_id', '=', 'journals.id')
            ->join('suppliers', 'accounts_payable_books.supplier_id', '=', 'suppliers.id')
            ->where('accounts_payable_books.user_id', $user_id)
            ->where('journals.account_date', '<', $params->date_from)
            ->groupBy('supplier_id')
            ->groupBy('suppliers.supplier_name')
            ->orderBy('id')
            ->get();

        // 今月の取引先id,取引先名を取得
        $current_month_suppliers = $this->payable->select('supplier_id as id', 'suppliers.supplier_name')
            ->selectRaw('0 as last_balance')
            ->join('journals', 'accounts_payable_books.journal_id', '=', 'journals.id')
            ->join('suppliers', 'accounts_payable_books.supplier_id', '=', 'suppliers.id')
            ->where('accounts_payable_books.user_id', $user_id)
            ->where('journals.account_date', '>=', $params->date_from)
            ->where('journals.account_date', '<', $params->date_to)
            ->orderBy('id')
            ->distinct()
            ->get();

        // 取得データを比較し、パターン判定
        $case = $this->get_record_case($last_month_suppliers, $current_month_suppliers);
        // 返却データ格納用配列
        $result = [];

        switch($case){
            case 1: // 両方とも無し
                return $result;
                break;
            case 2: // 先月まで無し、今月有り
                //return "// 先月まで無し、今月有り";
                foreach ($current_month_suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = $this->getPayableRecordItems($user_id, $params, $supplier);
                }
                break;
            case 3: // 先月まで有り、今月無し
                foreach ($last_month_suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = [];
                }
                break;
            case 4: // 先月まで有り、今月も有り、中身が不一致
                $new_suppliers = $this->array_map(['id'], $current_month_suppliers);
                $old_suppliers = $this->array_map(['id'], $last_month_suppliers);
                // 先月までのデータに今月のデータを結合する
                $suppliers = array_values($old_suppliers + $new_suppliers);

                foreach ($suppliers as $key => $supplier) {
                    $result[$key]['table_title'] = $supplier['supplier_name'];
                    $result[$key]['last_balance'] = $supplier['last_balance'];
                    $result[$key]['items'] = $this->getPayableRecordItems($user_id, $params, $supplier);
                }
                break;
        }
        return $result;
    }

    public function getExpensesRecord($params)
    {
        $user_id = Auth::id();

        // 先月までの経費id,経費名,残高情報を取得
        $last_month_expenses = $this->journal->select('account_subject_id as id', 'account_subject')
            ->selectRaw('sum(debit_amount) - sum(credit_amount) as last_balance')
            ->join('expense_books', 'journals.id', '=', 'expense_books.journal_id')
            ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
            ->where('journals.user_id', $user_id)
            ->where('account_date', '<', $params->date_from)
            ->groupBy('account_subject_id')
            ->groupBy('account_subject')
            ->orderBy('account_subject_id', 'asc')
            ->get();

        // 先月までの経費id,経費名,残高情報を取得
        $current_month_expenses = $this->journal->select('account_subject_id as id', 'account_subject')
            ->selectRaw('0 as last_balance')
            ->join('expense_books', 'journals.id', '=', 'expense_books.journal_id')
            ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
            ->where('journals.user_id', $user_id)
            ->where('account_date', '>=', $params->date_from)
            ->where('account_date', '<', $params->date_to)
            ->orderBy('account_subject_id', 'asc')
            ->distinct()
            ->get();

        // 取得データを比較し、パターン判定
        $case = $this->get_record_case($last_month_expenses, $current_month_expenses);
        // 返却データ格納用配列
        $result = [];

        switch($case){
            case 1: // 両方とも無し
                return $result;
                break;
            case 2: // 先月まで無し、今月有り
                foreach ($current_month_expenses as $key => $expenses) {
                    $result[$key]['table_title'] = $expenses['account_subject'];
                    $result[$key]['last_balance'] = $expenses['last_balance'];
                    $result[$key]['items'] = $this->getExpensesRecordItems($user_id, $params, $expenses);
                }
                break;
            case 3: // 先月まで有り、今月無し
                foreach ($last_month_expenses as $key => $expenses) {
                    $result[$key]['table_title'] = $expenses['account_subject'];
                    $result[$key]['last_balance'] = $expenses['last_balance'];
                    $result[$key]['items'] = [];
                }
                break;
            case 4: // 先月まで有り、今月も有り
                $new_expenses = $this->array_map(['id'], $current_month_expenses);
                $old_expenses = $this->array_map(['id'], $last_month_expenses);
                // 先月までのデータに今月のデータを結合する
                // 差分だけ追加する
                $expenses = array_values($old_expenses + $new_expenses);

                foreach ($expenses as $key => $expenses) {
                    $result[$key]['table_title'] = $expenses['account_subject'];
                    $result[$key]['last_balance'] = $expenses['last_balance'];
                    $result[$key]['items'] = $this->getExpensesRecordItems($user_id, $params, $expenses);
                }
                break;
        }
        return $result;
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

    public function getDepositRecordItems($user_id, $params, $deposit){
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
        $deposit_record_lists = $this->deposit
        ->select($column)
        ->join('journals', 'deposit_account_books.journal_id', '=', 'journals.id')
        ->where('journals.account_date', '>=', $params->date_from)
        ->where('journals.account_date', '<', $params->date_to)
        ->where('deposit_account_books.user_id', $user_id)
        ->where('deposit_account_books.bank_id', $deposit['id'])
        ->where('journals.account_subject_id', $deposit['account_subject_id'])
        ->orderBy('journals.account_date', 'asc')
        ->get();

        $result = [];
        foreach ($deposit_record_lists as  $deposit_record) {
            $target_account_type = ($deposit_record->journal_type === 0) ? 1 : 0;

            $items = $this->journal
                ->select($result_column)
                ->selectRaw("(CASE journal_type WHEN 0 THEN 1 WHEN 1 THEN 0 END) AS journal_type")
                ->join('account_subjects', 'journals.account_subject_id', '=', 'account_subjects.id')
                ->where('unit_number', $deposit_record->unit_number)
                ->where('journal_type', $target_account_type)
                ->get();
            foreach($items as $item){
                array_push($result, $item);
            }
        }
        return $result;
    }

    public function getReceivableRecordItems($user_id, $params, $supplier){
        $column = [
            'journals.id',
            'account_date',
            'summary',
            'journal_type',
            'amount',
        ];
        $result = $this->journal->select($column)
                        ->join('accounts_receivable_books', 'journals.id', '=', 'accounts_receivable_books.journal_id')
                        ->where('journals.account_date', '>=', $params->date_from)
                        ->where('journals.account_date', '<', $params->date_to)
                        ->where('journals.user_id', $user_id)
                        ->where('supplier_id', $supplier['id'])
                        ->orderBy('account_date', 'asc')
                        ->get();
        return $result;
    }

    public function getPayableRecordItems($user_id, $params, $supplier){
        $column = [
            'journals.id',
            'account_date',
            'summary',
            'journal_type',
            'amount',
        ];
        $result = $this->journal->select($column)
                        ->join('accounts_payable_books', 'journals.id', '=', 'accounts_payable_books.journal_id')
                        ->where('journals.account_date', '>=', $params->date_from)
                        ->where('journals.account_date', '<', $params->date_to)
                        ->where('journals.user_id', $user_id)
                        ->where('supplier_id', $supplier['id'])
                        ->orderBy('account_date', 'asc')
                        ->get();
        return $result;
    }


    public function getExpensesRecordItems($user_id, $params, $expenses){
        $column = [
            'journals.id',
            'account_date',
            'summary',
            'journal_type',
            'amount',
        ];
        $result = $this->journal->select($column)
            ->where('account_date', '>=', $params->date_from)
            ->where('account_date', '<', $params->date_to)
            ->where('user_id', $user_id)
            ->where('account_subject_id', $expenses['id'])
            ->orderBy('account_date', 'asc')
            ->get();

        return $result;
    }

    /** 2次元配列を特定のキーでマッピングするための関数
     * 
     * @var array $array
     * @var string $key
     * @return array 
     */
    public function array_map($keys, $array){
        $array_map = [];
        foreach ($array as $row) {
            $key = "";
            foreach($keys as $value){
                $key = $key . $row[$value];
            }
            $array_map[$key] = $row;
        }
        return $array_map;
    }

    /** 会計データ取得がどのケースか判定するための関数
     * 
     * @var array $last_month_records
     * @var array $current_month_records
     * @return integer
     */
    public function get_record_case($last_month_records, $current_month_records){
         // 取得データを比較し、パターン判定
         if ($last_month_records->isEmpty() && $current_month_records->isEmpty()) {
            // 両方とも無し:1
            $case = 1;
        } else if ($last_month_records->isEmpty() && !$current_month_records->isEmpty()) {
            // 先月まで無し、今月有り:2
            $case = 2;
        } else if (!$last_month_records->isEmpty() && $current_month_records->isEmpty()) {
            // 先月まで有り、今月無し:3
            $case = 3;
        } else {
            // 先月まで有り、今月有り:4
            $case = 4;
        }
        return $case;
    }

}
