<?php

use Illuminate\Database\Seeder;
use App\Models\DepositAccountBook;
use App\Models\DepositBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepositBalancesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 保存処理判定
        $count = DB::table('deposit_account_books')->count();

        if ($count) {
            DepositBalance::truncate();
            $user_ids = DepositAccountBook::select('user_id')->groupBy('user_id')->get();
        }
        foreach ($user_ids as $user_id) {
            $sql = "SELECT to_char(account_date,'YYYY-MM') AS month,
                            bank_id, account_subject_id,
                            sum(debit_amount) as debit, 
                            sum(credit_amount) as credit,
                            sum(debit_amount) - sum(credit_amount)  as balance
                        FROM journals join deposit_account_books on journals.id = deposit_account_books.journal_id
                        where deposit_account_books.user_id = :user_id
                        GROUP BY month, bank_id, account_subject_id order by month;";
            $data[':user_id'] = $user_id->user_id;
            $balance_data_lists = DB::select($sql, $data);


            foreach ($balance_data_lists as $balance_data) {
                DepositBalance::insert([
                    'account_month' => $balance_data->month,
                    'user_id' => $user_id->user_id,
                    'bank_id' => $balance_data->bank_id,
                    'deposit_item_id' => $balance_data->account_subject_id,
                    'balance' => $balance_data->balance,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
