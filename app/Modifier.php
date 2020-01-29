<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Modifier extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'modifiers';

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
     * Belongs To relation Company
     */
    public function company()
    {
    	return $this->belongsTo(Company::class, 'company_id');
    }
    
    /**
     * has Many relation Company
     */
    public function modifier_options()
    {
    	return $this->hasMany(Modifier_option::class, 'modifier_id');
    }
        
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();
        
        static::addGlobalScope('company_id', function (Builder $builder) {
            $builder->where('company_id',  Auth::id());
        });
        
    	static::deleting(function($modifier) {
            $modifier->modifier_options()->delete();             
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name','max_options'];
    
	
}
