<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AccountSbujectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_subjects')->insert([
            [ 'account_subject' => '現金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '普通預金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '売掛金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '事業主貸', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '買掛金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '短期借入金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '長期借入金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '未払費用', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '預り金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '事業主借', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '売上金', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '経費', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
            [ 'account_subject' => '雑収入', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ],
        ]);
    }
}
