<?php

namespace App;

use App\Notifications\CustomerResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserDevice extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'user_devices';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','device_token','device_type','user_id','app_mode','user_type','device_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}