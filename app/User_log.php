<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_log extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_logs';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';
    
    
    /**
     * belongs To relation user
     */
    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }
        
    
    
    /**
     * boot
     */
    protected static function boot ()
    {
    	parent::boot();

    	static::deleting(function($categories) {
    	});
    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'description', 'ip', 'agent','session_time'];
    
    
}
