<?php

namespace App;

use App\Notifications\CustomerResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class FloorTable extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id';

    /**
     * belongs To relation User
     */

    public function floor()
    {
        return $this->belongsTo(Floor::class,'floor_id');
    }
    public function waiter()
    {
        return $this->belongsTo(User::class,'waiter_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','table_id','floor_id','name','book_status','order_id','waiter_id','x1y1','x2y2','seats','table_image','tag_id','is_mobile_order','table_number'
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
