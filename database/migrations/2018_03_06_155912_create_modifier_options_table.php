<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModifierOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modifier_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modifier_id')->index()->unsigned();
            $table->foreign('modifier_id')->references('id')->on('modifiers')->onDelete('cascade');            
            $table->string('name', 200);            
            $table->integer('cost');            
            $table->integer('price');            
            $table->string('sku', 200);            
            $table->integer('ordering');            
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
        Schema::drop('modifier_options');
    }
}
