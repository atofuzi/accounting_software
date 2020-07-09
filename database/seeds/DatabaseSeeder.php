<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 各テーブルへのデータの流し込みを呼び出す
        $this->call(GentiansTableSeeder::class);
        $this->call(AccountSbujectsTableSeeder::class);
        // $this->call(UserSeeder::class);
    }
}
