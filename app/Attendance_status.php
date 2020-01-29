<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance_status extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'attendance_status';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date','status','attendance_time'
    ];

   
}
