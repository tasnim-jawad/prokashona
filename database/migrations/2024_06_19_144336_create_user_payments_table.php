<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('account_log_id')->nullable();

            $table->bigInteger('user_id')->nullable();
            $table->date('date')->nullable();
            $table->string('trx_id', 20)->nullable();
            $table->float('amount')->nullable();

            $table->enum('type', ['debit', 'credit'])->nullable();

            $table->string('reference_table_name', 150)->nullable();
            $table->bigInteger('reference_table_id')->nullable();
            $table->enum('transaction_type', [
                'customer_advance',
                'customer_order',
                'customer_refund',
            ])->nullable();

            $table->string('payment_method',20)->nullable();
            $table->bigInteger('account_id')->nullable();
            $table->bigInteger('account_number_id')->nullable();

            $table->bigInteger('creator')->unsigned()->nullable();
            $table->string('slug',50)->nullable();
            $table->tinyInteger('status')->unsigned()->default(1);
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
        Schema::dropIfExists('user_payments');
    }
}
