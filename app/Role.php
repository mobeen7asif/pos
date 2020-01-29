<?php

namespace App;

use App\Notifications\CompanyResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class Role extends \Spatie\Permission\Models\Role
{
    
    
    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        if (static::where('name', $attributes['name'])->where('guard_name', $attributes['guard_name'])->where('company_id', \Auth::id())->first()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        if (isNotLumen() && app()::VERSION < '5.4') {
            return parent::create($attributes);
        }

        return static::query()->create($attributes);
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
        
    	static::deleting(function($currencies) {
    		
    	});
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'name', 'guard_name',
    ];    

    
}
