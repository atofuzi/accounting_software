<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file_path = '/Applications/MAMP/htdocs/accounting_software/database/seeds/supplier.csv';
        $file = new \SplFileObject($file_path);
        $file->setFlags(
            \SplFileObject::READ_CSV  |
                \SplFileObject::READ_AHEAD  | // 先読み／巻き戻しで読み込み
                \SplFileObject::SKIP_EMPTY  |  // 空行を読み飛ばす
                \SplFileObject::DROP_NEW_LINE // 行末の改行を読み飛ばす
        );
        foreach ($file as $line)
            DB::table('suppliers')->insert([
                ['user_id' => $line[1], 'supplier_name' => $line[2], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ]);
    }
}
