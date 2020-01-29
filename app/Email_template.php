<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;
use Mail;

class Email_template extends Model
{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_templates';

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
    

    public static function sendEmail($to,$email_data,$body,$store_id = 0)
    {

        if($store_id > 0){
            $res['final_content'] = $body;
            $store = Store::find($store_id);
            if($store){
                $username = $store->user_name;
                $password = $store->password;
                $host = $store->host;
                $backup = \Mail::getSwiftMailer();
                if(isset($username) && isset($password) && isset($host)){
                    //setting new swift mailer
                    $transport = (new \Swift_SmtpTransport($host, '587'))
                        ->setUsername($username)
                        ->setPassword($password)
                        ->setEncryption('tls');

                    \Mail::setSwiftMailer(new \Swift_Mailer($transport));
                    try {
                        Mail::send('emails.email_body',$res, function ($message) use ($email_data, $to) {
                            $message->from('info@skulocity.com', $email_data->name);
                            $message->to($to, $to)->subject($email_data->subject);
                        });
                    }catch (\Exception $e) {
                        //echo $e->getMessage();
                    }
                }
                else {
                    Mail::setSwiftMailer($backup);
                    try {
                        Mail::send('emails.email_body',$res, function ($message) use ($email_data, $to) {
                            $message->from('info@skulocity.com', $email_data->name);
                            $message->to($to, $to)->subject($email_data->subject);
                        });
                    }catch (\Exception $e) {
                        //echo $e->getMessage();
                    }
                }
            }
        }
        else {
            try {
                $res['final_content'] = $body;
                Mail::send('emails.email_body',$res, function ($message) use ($email_data, $to) {
                    $message->from('info@skulocity.com', $email_data->name);
                    $message->to($to, $to)->subject($email_data->subject);
                });
            }catch (\Exception $e) {
                //echo $e->getMessage();
            }
        }

    }
    
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'subject', 'from', 'content','template_key','company_id'];
    
	
}
