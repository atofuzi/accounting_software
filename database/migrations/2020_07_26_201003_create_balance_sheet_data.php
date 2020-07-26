<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceSheetData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_sheet_data', function (Blueprint $table) {
            $table->text('account_month');
            $table->integer('user_id');
            $table->integer('cash')->default(0);
            $table->integer('normal_deposit')->default(0);
            $table->integer('current_account')->default(0);
            $table->integer('time_deposit')->default(0);
            $table->integer('other_deposit')->default(0);
            $table->integer('bills_receivable')->default(0);
            $table->integer('accounts_receivable')->default(0);
            $table->integer('valuable_securities')->default(0);
            $table->integer('inventory')->default(0);
            $table->integer('prepayment')->default(0);
            $table->integer('loan')->default(0);
            $table->integer('building')->default(0);
            $table->integer('building_accessories')->default(0);
            $table->integer('machinery')->default(0);
            $table->integer('vehicle_carrier')->default(0);
            $table->integer('tool_equipment')->default(0);
            $table->integer('land')->default(0);
            $table->integer('business_owner_loan')->default(0);
            $table->integer('bills_payable')->default(0);
            $table->integer('accounts_payable')->default(0);
            $table->integer('debt')->default(0);
            $table->integer('unpaid')->default(0);
            $table->integer('advance_receipt_money')->default(0);
            $table->integer('custody_money')->default(0);
            $table->integer('allowance_for_doubtful_accounts')->default(0);
            $table->integer('business_owner_debit')->default(0);
            $table->integer('owned_capital')->default(0);
            $table->integer('earning_amount')->default(0);
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
        Schema::dropIfExists('balance_sheet_data');
    }
}
