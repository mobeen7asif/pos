<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function(Blueprint $table) {
                $table->increments('id');  
                $table->integer('store_id')->index()->unsigned();
                $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
                $table->integer('parent_id');               
                $table->string('category_name');
                $table->string('category_image')->nullable()->default('NULL');
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
        Schema::drop('categories');
    }
}
