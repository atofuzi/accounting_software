<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitNumberInDepositAccountBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposit_account_books', function (Blueprint $table) {
            $table->string('unit_number', 255)->nullable('true');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposit_account_books', function (Blueprint $table) {
            $table->dropColumn('unit_number');
        });
    }
}
