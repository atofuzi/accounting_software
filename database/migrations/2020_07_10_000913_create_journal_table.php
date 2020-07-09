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
                $table->integer('account_month'); // 会計月
                $table->text('account_date'); // 会計日
                $table->integer('account_subject_id'); // 会計科目id
                $table->text('summary')->nullable(); // 摘要
                $table->integer('gentian_number'); // 元丁番号
                $table->integer('bank_id')->nullable(); // 銀行id
                $table->integer('accounts_receivable_id')->nullable(); // 売掛金id
                $table->integer('accounts_payable_id')->nullable(); // 買掛金id
                $table->integer('expens_subjects_id')->nullable(); // 経費科目id
                $table->integer('amount'); // 金額
                $table->integer('ledgers_id')->nullable(); // 元帳id 
                $table->integer('journal_flg')->nullable(); // 仕分けフラグ 0=借方 1=貸方 
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
