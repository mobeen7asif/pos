<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySettingsTable extends Migration {

    public function up()
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->index()->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade'); 
            $table->integer('currency_id')->index()->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');    
            $table->string('email');
            $table->integer('store_id')->index()->unsigned();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');   
            $table->integer('tax_id')->index()->unsigned();
            $table->foreign('tax_id')->references('id')->on('tax_rates')->onDelete('cascade');   
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
        Schema::drop('company_settings');
    }

}
