<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // nhap kho
        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('w_product_id')->index();
            $table->integer('w_price')->default(0);
            $table->integer('w_qty')->default(0);
            $table->dateTime('w_time_warehouse')->nullable();
            $table->timestamps();
        });
        Schema::create('exports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('w_order_id')->index();
            $table->integer('w_price')->default(0);
            $table->integer('w_qty')->default(0);
            $table->dateTime('w_time_exports')->nullable();
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
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('exports');
    }
}
