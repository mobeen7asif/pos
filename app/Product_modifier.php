<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Product_modifier extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_modifiers';

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
     * belongs To relation Product
     */
    public function modifier()
    {
    	return $this->belongsTo(Product::class);
    }
    
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
    protected $fillable = ['product_id', 'modifier_id'];
    
	
}
