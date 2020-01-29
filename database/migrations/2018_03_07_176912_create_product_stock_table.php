<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->index()->unsigned();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade'); 
            $table->integer('product_id')->index()->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
            $table->integer('product_variant_id')->index()->unsigned();
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');                        
            $table->integer('quantity')->default(0);                       
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
        Schema::drop('product_stock');
    }
}
