<?php

namespace App\Helpers;
use Request;
use App\User_log;
use App\User;


class LogActivity
{


    public static function addToUserLog($user_id,$name,$description)
    {
    	$log = [];
        
        if(is_int($user_id)) {
           $log['user_id'] = $user_id;
        }elseif(is_string($user_id)){
           $user = User::where('email',$user_id)->first();
           if($user)
              $log['user_id'] = $user->id; 
           else
              $log['user_id'] = 0; 
        }else{
            $log['user_id'] = 0;
        }
    	
    	
        $log['name'] = $name;
        $log['description'] = $description;
    	$log['ip'] = Request::ip();
    	$log['agent'] = Request::header('user-agent');
    	
    	User_log::create($log);
    }


    public static function getUserLogs($user_id)
    {
    	return User_log::where('user_id',$user_id)->latest()->get();
    }


}