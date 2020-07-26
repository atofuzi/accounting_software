<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfitLossData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_loss_data', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('sales_amount')->default(0);
            $table->integer('purchase_amount')->default(0);
            $table->integer('tax_public_dues')->default(0);
            $table->integer('packing_freight')->default(0);
            $table->integer('utility_costs')->default(0);
            $table->integer('travel_expenses')->default(0);
            $table->integer('communication_fee')->default(0);
            $table->integer('advertising_expenses')->default(0);
            $table->integer('entertainment_fee')->default(0);
            $table->integer('non_life_insurance_premium')->default(0);
            $table->integer('repair_costs')->default(0);
            $table->integer('supplies_expense')->default(0);
            $table->integer('depreciation')->default(0);
            $table->integer('welfare_expense')->default(0);
            $table->integer('salary_wage')->default(0);
            $table->integer('outsourced_wage')->default(0);
            $table->integer('interest_discount')->default(0);
            $table->integer('rent')->default(0);
            $table->integer('bad_debt')->default(0);
            $table->integer('software_usage_fee')->default(0);
            $table->integer('newspaper_book_fee')->default(0);
            $table->integer('miscellaneous_expenses')->default(0);
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
        Schema::dropIfExists('profit_loss_data');
    }
}
