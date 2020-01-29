<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Variant extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'variants';

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
     * belongs To relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }
    
    /**
     * has Many relation product attributes
     */
    public function product_attributes()
    {
    	return $this->hasMany(Product_attribute::class, 'variant_id');
    }
    
    
    /**
     * Scopes
    */    
    
    /**
     * Scope a query to only include popular users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query and $company_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompany (Builder $query, $company_id) {
        
        return $query->whereHas('company', function ($q) use ($company_id) {
                $q->where('company_id', $company_id);
        });
        
    }
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();        
        
    	static::deleting(function($variant) {
    		
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name'];
    
	
}
