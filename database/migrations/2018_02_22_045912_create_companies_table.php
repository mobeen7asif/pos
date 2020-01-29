<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('country',10);
            $table->string('state',100);
            $table->string('city',100);
            $table->string('zip',30)->nullable();
            $table->text('address')->nullable();
            $table->string('phone',30)->nullable();
            $table->string('mobile',30)->nullable();
            $table->string('logo',100)->nullable();           
            $table->rememberToken();
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
        Schema::drop('companies');
    }
}
