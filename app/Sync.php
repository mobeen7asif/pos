<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Sync extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */ 
    protected $table = 'sync';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';       
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['sync_type','sync_id', 'store_id'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['updated_at'];
    
}
