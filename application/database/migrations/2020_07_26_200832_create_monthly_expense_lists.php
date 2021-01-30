<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyExpenseLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_expense_lists', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->text('expense_name');
            $table->text('claim_tate');
            $table->text('payment_date');
            $table->text('payment_method');
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
        Schema::dropIfExists('monthly_expense_lists');
    }
}
