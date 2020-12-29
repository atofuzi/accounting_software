<?php

use Illuminate\Database\Seeder;
use App\Models\DepositAccountBook;
use App\Models\DepositBalance;

class DepositAccountBoooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DepositAccountBook::count() > 0 && DepositAccountBook::whereNull('unit_number')->count() > 0) {
            print_r('seeder実行');
            $column = [
                'journals.unit_number',
                'deposit_account_books.journal_id'
            ];
            $depositAccountJournals = DepositAccountBook::select($column)->join('journals', 'deposit_account_books.journal_id', '=', 'journals.id')->get();
            foreach ($depositAccountJournals as $journal) {
                DepositAccountBook::where('journal_id', $journal->journal_id)->update(['unit_number' => $journal->unit_number]);
            }
        }
    }
}
