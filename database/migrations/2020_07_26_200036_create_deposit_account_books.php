<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositAccountBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_account_books', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('journal_id');
            $table->integer('journal_type');
            $table->integer('bank_id');
            $table->integer('deposit_item');
            $table->integer('deposit_item_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposit_account_books');
    }
}
