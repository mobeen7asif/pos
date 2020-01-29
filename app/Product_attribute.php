<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Product_attribute extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_attributes';

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
     * belongs To relation variant
     */
    public function variant()
    {
    	return $this->belongsTo(Variant::class, 'variant_id');
    }
    
    /**
     * has One relation product_stock
     */
    public function product_stock()
    {
    	return $this->hasOne(Product_stock::class, 'product_variant_id');
    }
    
    /**
     * has Many relation product variants
     */
    public function product_variants()
    {
    	return $this->hasMany(Product_variant::class, 'product_attribute_id');
    }
        
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();        
        
    	static::deleting(function($product_variant) {
    		//$product_variant->product_stock()->delete();
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['product_id', 'variant_id'];
    
	
}
