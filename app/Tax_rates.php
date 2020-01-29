<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Tax_rates extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tax_rates';

    /** 
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    /**
     * belongs To relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }
    
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
        
//        static::addGlobalScope('company_id', function (Builder $builder) {
//            $builder->where('company_id',  Auth::id());
//        });
        
    	static::deleting(function($tax_rates) {
    		
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','code', 'name', 'rate'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	
}
