<?php

use Illuminate\Database\Seeder;
use App\Journal;
use App\Gentian;
use App\Bank;
use App\Supplier;
use App\AccountsReceivableBook;
use App\AccountsPayableBook;
use App\ExpenseBook;
use App\AccountsReceivableBalance;
use App\AccountSubject;
use App\DepositAccountBook;
use App\AccountsReceivable;
use App\BalanceSheetData;
use App\ProfitLossData;
use App\DepositBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestDataInsert extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */

    public function run()
    {
        $user_id = 3;
        $journal = new journal;

        // 会計データの読み込み
        $file_path = '/Applications/MAMP/htdocs/accounting_software/database/seeds/journals_test_data.csv';
        $file = new \SplFileObject($file_path);
        $file->setFlags(
            \SplFileObject::READ_CSV  |
                \SplFileObject::READ_AHEAD  | // 先読み／巻き戻しで読み込み
                \SplFileObject::SKIP_EMPTY  |  // 空行を読み飛ばす
                \SplFileObject::DROP_NEW_LINE // 行末の改行を読み飛ばす
        );
        foreach ($file as $line) {
            $journalId = DB::table('journals')
                    ->insertGetId([
                        'user_id' => $line[1],
                        'account_date' => $line[0],
                        'account_subject_id' => $line[2],
                        'summary' => $line[3],
                        'gentian_number' => $line[4],
                        'amount' => $line[5],
                        'journal_type' => $line[6],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);

            // 読み込みと同時に預金元帳、売掛金元帳、経費元帳、買掛金元帳、現金元帳を作成
            // 預金判定：account_subject_id 2〜5
            if ($line[2] >= 2 && $line[2] <= 5) {
                // 預金元帳を作成する
                $this->insertDepositAccountBooks($line, $journalId);
            }

            // 売掛金判定：account_subject_id 7
            if ($line[2] == 7) {
                // 売掛金元帳を作成する
                $this->insertAccountsReceivableBooks($line, $journalId);
            }

            // 買掛金判定：account_subject_id 20
            if ($line[2] == 20) {
                // 買掛金元帳を作成する
                $this->insertAccountsPayableBooks($line, $journalId);
            }

            // 経費判定：account_subject_id 32〜52
            if ($line[2] >= 32 && $line[2] <= 52) {
                // 経費元帳を作成する
                $this->insertExpenseBooks($line, $journalId);
            }
        }
    }
    /**
     * 預金元帳テーブルへの登録（DepositAccountBook)
     * 
     * $data：会計データ
     * $id：会計データを登録したjournals.id
     * @return void
     *  
     */
    public function insertDepositAccountBooks($data, $journalId)
    {
        $bankId = !empty($data[7]) ? $data[7] : 1;
        $depositBook = new DepositAccountBook;
        $depositBook->insert([
            'user_id' => $data[1],
            'journal_id' => $journalId,
            'journal_type' => $data[6],
            'bank_id' => $bankId,
            'deposit_item_id' => $data[2],
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
    public function insertAccountsReceivableBooks($data, $journalId)
    {
        $supplierId = !empty($data[7]) ? $data[7] : 1;
        $accountsReceivableBook = new AccountsReceivableBook;
        $accountsReceivableBook->insert([
            'user_id' => $data[1],
            'journal_id' => $journalId,
            'journal_type' => $data[6],
            'supplier_id' => $supplierId,
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
    public function insertAccountsPayableBooks($data, $journalId)
    {
        $supplierId = !empty($data[7]) ? $data[7] : 1;
        $accountsPayableBook = new AccountsPayableBook;
        $accountsPayableBook->insert([
            'user_id' => $data[1],
            'journal_id' => $journalId,
            'journal_type' => $data[6],
            'supplier_id' => $supplierId,
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
    public function insertExpenseBooks($data, $journalId)
    {
        $expenseBook = new ExpenseBook;
        $expenseBook->insert([
            'user_id' => $data[1],
            'journal_id' => $journalId,
            'journal_type' => $data[6],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
