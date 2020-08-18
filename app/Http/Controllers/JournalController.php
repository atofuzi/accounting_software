<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\UseAccountsSubject;
use App\AccountsReceivableBook;
use App\AccountsPayableBook;
use App\ExpenseBook;
use App\DepositAccountBook;

class JournalController extends Controller
{
    public function index()
    {
        return view('journal');
    }

    public function register(Request $request)
    {
                // 会計日
                $account_date = $request->account_date;
                // ユーザID取得
                $user_id = Auth::id();
                
                // requestをdepositとcreditに分け、それぞれDBに格納
                foreach ($request->items as $journal_data) {
                    if (!empty(array_filter($journal_data['debit']))) {
                        $journal_id = DB::table('journals')
                                            ->insertGetId([
                                                'user_id' => $user_id,
                                                'account_date' => $account_date,
                                                'account_subject_id' => $journal_data['debit']['account_subject_id'],
                                                'summary' => $journal_data['debit']['summary'],
                                                'amount' => $journal_data['debit']['amount'],
                                                'journal_type' => 0,
                                                'created_at' => Carbon::now(),
                                                'updated_at' => Carbon::now()
                                            ]);

                        // 読み込みと同時に預金元帳、売掛金元帳、経費元帳、買掛金元帳、現金元帳を作成
                        
                        $account_subject_id = $journal_data['debit']['account_subject_id'];
                        
                        // 預金判定：account_subject_id 2〜5
                        if ($account_subject_id >= 2 && $account_subject_id <= 5) {
                        // 預金元帳を作成する
                            $this->insertDepositAccountBooks($journal_data['debit'], $journal_id, $user_id);
                        }

                        // 売掛金判定：account_subject_id 7
                        if ($account_subject_id == 7) {
                            // 売掛金元帳を作成する
                            $this->insertAccountsReceivableBooks($journal_data['debit'], $journal_id, $user_id);
                        }

                        // 買掛金判定：account_subject_id 20
                        if ($account_subject_id == 20) {
                            // 買掛金元帳を作成する
                            $this->insertAccountsPayableBooks($journal_data['debit'], $journal_id, $user_id);
                        }

                        // 経費判定：account_subject_id 32〜52
                        if ($account_subject_id >= 32 && $account_subject_id <= 52) {
                            // 経費元帳を作成する
                            $this->insertExpenseBooks($journal_data['debit'], $journal_id, $user_id);
                        }
                    }
                }    
        
                foreach ($request->items as $journal_data) {
                    if (!empty(array_filter($journal_data['credit']))) {
                        $journal_id = DB::table('journals')
                                            ->insertGetId([
                                                'user_id' => $user_id,
                                                'account_date' => $account_date,
                                                'account_subject_id' => $journal_data['credit']['account_subject_id'],
                                                'summary' => $journal_data['credit']['summary'],
                                                'amount' => $journal_data['credit']['amount'],
                                                'journal_type' => 1,
                                                'created_at' => Carbon::now(),
                                                'updated_at' => Carbon::now()
                                            ]);

                        $account_subject_id = $journal_data['credit']['account_subject_id'];
    
                        // 預金判定：account_subject_id 2〜5
                        if ($account_subject_id >= 2 && $account_subject_id <= 5) {
                        // 預金元帳を作成する
                            $this->insertDepositAccountBooks($journal_data['credit'], $journal_id, $user_id);
                        }

                        // 売掛金判定：account_subject_id 7
                        if ($account_subject_id == 7) {
                            // 売掛金元帳を作成する
                            $this->insertAccountsReceivableBooks($journal_data['credit'], $journal_id, $user_id);
                        }

                        // 買掛金判定：account_subject_id 20
                        if ($account_subject_id == 20) {
                            // 買掛金元帳を作成する
                            $this->insertAccountsPayableBooks($journal_data['credit'], $journal_id, $user_id);
                        }

                        // 経費判定：account_subject_id 32〜52
                        if ($account_subject_id >= 32 && $account_subject_id <= 52) {
                            // 経費元帳を作成する
                            $this->insertExpenseBooks($journal_data['credit'], $journal_id, $user_id);
                        }
                    }
                }

        return ['message' => 'success'];

    }

    public function getUseAccountSubjects($userId){
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
    public function getGentianNumbers(){
        $column = [
            'gentian_number',
            'account_subject_id'
        ];
        $gentianNumbers = DB::table('gentians')
                                ->select($column)
                                ->get();
        return $gentianNumbers;
    }
    public function getBankLists(){
        $banks = DB::table('banks')
                        ->select('id')
                        ->selectRaw('bank_name AS name')
                        ->get();
        return $banks;
    }
    public function getSupplierLists(){
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
    public function insertDepositAccountBooks($data, $journal_id, $user_id)
    {
        $bank_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $deposit_book = new DepositAccountBook;
        $deposit_book->insert([
            'user_id' => $user_id,
            'journal_id' => $journal_id,
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
    public function insertAccountsReceivableBooks($data, $journal_id, $user_id)
    {
        $supplier_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $accounts_receivable_book = new AccountsReceivableBook;
        $accounts_receivable_book->insert([
            'user_id' => $user_id,
            'journal_id' => $journal_id,
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
    public function insertAccountsPayableBooks($data, $journal_id, $user_id)
    {
        $supplier_id = !empty($data['add_info_id']) ? $data['add_info_id'] : 1;
        $accountsPayableBook = new AccountsPayableBook;
        $accountsPayableBook->insert([
            'user_id' => $user_id,
            'journal_id' => $journal_id,
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
    public function insertExpenseBooks($data, $journal_id, $user_id)
    {
        $expenseBook = new ExpenseBook;
        $expenseBook->insert([
            'user_id' => $user_id,
            'journal_id' => $journal_id,
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
    
    public static function get($id){
        $account_subjects = self::$account_subjects;
        $account_subject = array_search($id,$account_subjects);
        return $account_subject;
    }
}