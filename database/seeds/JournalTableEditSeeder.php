<?php

use Illuminate\Database\Seeder;
use App\Models\Journal;

class JournalTableEditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 会計データを全件取得
        // 会計タイプ毎にdebit_amountとcredit_amountにデータを格納
        $journals = Journal::all();

        foreach ($journals as $journal) {
            if ($journal->journal_type === 0) {
                Journal::where('id', $journal->id)->update(['debit_amount' => $journal->amount]);
            } else {
                Journal::where('id', $journal->id)->update(['credit_amount' => $journal->amount]);
            }
        }
    }
}
