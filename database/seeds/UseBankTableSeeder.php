<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UseBankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('use_banks')->insert([
            ['user_id' => 1, 'bank_id' =>  1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['user_id' => 1, 'bank_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
