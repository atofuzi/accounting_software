<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('banks')->insert([
            ['bank_name' => 'ゆうちょ銀行', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bank_name' => '八十二銀行', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bank_name' => 'SBI住信ネット銀行', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bank_name' => '楽天銀行', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['bank_name' => '飯田信用金庫', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
