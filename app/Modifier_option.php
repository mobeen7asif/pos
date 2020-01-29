<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Modifier_option extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'modifier_options';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['updated_at'];
    
    /**
     * has Many relation Company
     */
    public function modifier()
    {
    	return $this->belongsTo(Modifier::class, 'modifier_id');
    }
        
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();        
        
    	static::deleting(function($currencies) {
    		
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['modifier_id', 'name','cost', 'price', 'sku'];
    
	
}
