m<?php

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
        $this->call(AccountSubjectsTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(GentiansTableSeeder::class);
        $this->call(UseAccountSubjectsTableSeeder::class);
        $this->call(SupplierTableSeeder::class);
        //$this->call(UserSeeder::class);
        $this->call(TestDataInsert::class);
    }
}
