<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('franchise_id')->nullable();
            $table->unsignedBigInteger('deliveryboy_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('cash_debit');
            $table->double('total_amount',8,2);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('franchise_id')->references('id')->on('franchises')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('deliveryboy_id')->references('id')->on('admins')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_invoices');
    }
}
