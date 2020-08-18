<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {

            $table->id();
            $table->integer('user_id');
            $table->date('account_date'); // 会計日
            $table->integer('account_subject_id'); // 会計科目id
            $table->text('summary')->nullable(); // 摘要
            $table->integer('amount'); // 金額
            $table->integer('journal_type'); // 仕訳方 0=借方 1=貸方 
            $table->timestamps(); //登録日・更新日
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journals');
    }
}
