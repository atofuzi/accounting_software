<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AccountSubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = '/Applications/MAMP/htdocs/accounting_software/database/seeds/account_subject.csv';
        $file = new \SplFileObject($file_path);
        $file->setFlags(
            \SplFileObject::READ_CSV  |
                \SplFileObject::READ_AHEAD  | // 先読み／巻き戻しで読み込み
                \SplFileObject::SKIP_EMPTY  |  // 空行を読み飛ばす
                \SplFileObject::DROP_NEW_LINE // 行末の改行を読み飛ばす
        );
        foreach ($file as $line)
            DB::table('account_subjects')->insert([
                ['account_subject' => $line[1], 'key_name' => $line[2], 'bs_pl_type' => $line[3], 'bs_type_small' => $line[4], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);
    }
}
