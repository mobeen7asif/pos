<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_providers extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_providers';
    
    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';        
           
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'token','provider_id', 'provider_name','active'];

}
