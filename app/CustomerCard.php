<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCard extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_cards';

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
    protected $fillable = ['customer_id', 'name', 'number', 'expiry','token', 'type','is_default'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

   protected $hidden = [
       'created_at','updated_at','deleted_at'
   ];
   
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
}
