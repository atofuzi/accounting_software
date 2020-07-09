<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GentiansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gentians')->insert( ['gentian_number' => 1 , 'account_subject_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 2 , 'account_subject_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 3 , 'account_subject_id' => 3, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 4 , 'account_subject_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 10 , 'account_subject_id' => 5, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 11 , 'account_subject_id' => 6, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 12 , 'account_subject_id' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 13 , 'account_subject_id' => 9, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 14 , 'account_subject_id' => 10, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 15 , 'account_subject_id' => 11, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 20 , 'account_subject_id' => 12, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 21 , 'account_subject_id' => 13, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
        DB::table('gentians')->insert( ['gentian_number' => 22 , 'account_subject_id' => 14, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now() ]);
    }
}
