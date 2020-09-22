<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UseAccountsSubject;
use App\Models\AccountsReceivableBook;
use App\Models\AccountsPayableBook;
use App\Models\ExpenseBook;
use App\Models\DepositAccountBook;
use App\Services\RecordService;
use App\Http\Requests\JournalValidationRequest;

class RecordController extends Controller
{
    private $record_service = null;

    public function __construct(RecordService $record_service)
    {
        $this->record_service = $record_service;
    }

    public function recordJournal(Request $request)
    {
        $result = $this->record_service->journal($request);
        return $result;
    }

    public function getSupplierLists()
    {
        $suppliers = DB::table('suppliers')
            ->select('id')
            ->selectRaw('supplier_name AS name')
            ->get();
        return $suppliers;
    }



    /**
     * 預金元帳テーブルへの登録（DepositAccountBook)
     * 
     * $data：会計データ
     * $id：会計データを登録したjournals.id
     * @return void
     *  
     */
    public function insertDepositAccountBooks($data, $journalId, $userId)
    {
        $bank_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $deposit_book = new DepositAccountBook;
        $deposit_book->insert([
            'user_id' => $userId,
            'journal_id' => $journalId,
            'bank_id' => $bank_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * 売掛金元帳テーブルへの登録（AccountsReceivableBook)
     * 
     * $data：会計データ
     * $id：会計データを登録したjournals.id
     * @return void
     *  
     */
    public function insertAccountsReceivableBooks($data, $journalId, $userId)
    {
        $supplier_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $accounts_receivable_book = new AccountsReceivableBook;
        $accounts_receivable_book->insert([
            'user_id' => $userId,
            'journal_id' => $journalId,
            'supplier_id' => $supplier_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * 買掛金元帳テーブルへの登録（AccountsPayableBook)
     * 
     * $data：会計データ
     * $id：会計データを登録したjournals.id
     * @return void
     *  
     */
    public function insertAccountsPayableBooks($data, $journalId, $userId)
    {
        $supplier_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $accountsPayableBook = new AccountsPayableBook;
        $accountsPayableBook->insert([
            'user_id' => $userId,
            'journal_id' => $journalId,
            'supplier_id' => $supplier_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * 経費元帳テーブルへの登録（ExpenseBook)
     * 
     * $data：会計データ
     * $id：会計データを登録したjournals.id
     * @return void
     *  
     */
    public function insertExpenseBooks($data, $journalId, $userId)
    {
        $expenseBook = new ExpenseBook;
        $expenseBook->insert([
            'user_id' => $userId,
            'journal_id' => $journalId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
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

    public static function get($id)
    {
        $account_subjects = self::$account_subjects;
        $account_subject = array_search($id, $account_subjects);
        return $account_subject;
    }
}
