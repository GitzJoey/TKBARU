<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->default(0);
            $table->unsignedBigInteger('product_id')->default(0);
            $table->unsignedBigInteger('stock_id')->default(0);
            $table->unsignedBigInteger('selected_product_unit_id')->default(0);
            $table->unsignedBigInteger('base_unit_id')->default(0);
            $table->decimal('conversion_value', 19, 2)->default(0);
            $table->decimal('quantity', 19, 2)->default(0);
            $table->decimal('to_base_quantity', 19, 2)->default(0);
            $table->decimal('price', 19, 2)->default(0);
            $table->decimal('discount', 19, 2)->default(0);
            $table->unsignedBigInteger('created_by')->default(0);
            $table->unsignedBigInteger('updated_by')->default(0);
            $table->unsignedBigInteger('deleted_by')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('items');
    }
}
