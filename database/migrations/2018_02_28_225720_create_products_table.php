<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::create('products', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->index()->unsigned();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->string('name');
                $table->string('code',100);
                $table->string('sku',100);
                $table->integer('supplier_id');
                $table->integer('tax_rate_id')->index()->unsigned();
                $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->onDelete('cascade');
                $table->tinyInteger('type')->default(0)->comment('Standard: 1, Combo: 2');
                $table->string('barcode_symbology',10);
                $table->string('cost',100)->nullable(true);
                $table->string('price',100);
                $table->tinyInteger('is_variants')->default(0)->comment('Yes: 1, No: 0');
                $table->tinyInteger('is_modifier')->default(0)->comment('Yes: 1, No: 0');
                $table->integer('discount_type')->default(0);
                $table->integer('discount')->default(0);
                $table->tinyInteger('tax_method')->default(0)->comment('Exclusive: 1, Inclusive: 2');
                $table->text('detail')->nullable(true);
                $table->text('invoice_detail')->nullable(true);
                $table->tinyInteger('is_active')->default(1)->comment('Active: 1, Iactive: 0');
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
        Schema::drop('products');
    }

}
