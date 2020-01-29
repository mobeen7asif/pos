<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Currency extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string 
     */
    protected $table = 'currencies';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';    
    
    /**
     * has Many relation Company
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
        
    	static::deleting(function($currencies) {
    		
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id','code', 'name', 'symbol','direction'];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at','deleted_at'];
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];
	
}
