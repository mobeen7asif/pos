<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::create('product_images', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id');
                $table->string('name');
                $table->string('default')->default(0)->comment('1: Default Image');
		$table->tinyInteger('is_active')->default(1);

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
        Schema::drop('product_images');
    }

}
