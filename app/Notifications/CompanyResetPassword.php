<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Email_template;

class CompanyResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        
        
        $email_data = Email_template::where('key','forgot_password')->first();
        
        $email_to = $notifiable->email;
        $email_body = $email_data->content;
        $reset_link = '<a href="'.url('company/password/reset', $this->token).'">Reset Password</a>';
        
        $email_body = str_replace('{user_name}',$notifiable->name,$email_body);
        $email_body = str_replace('{reset_password_link}',$reset_link,$email_body);           
        $email_body = str_replace('{site_name}',settingValue('site_title'),$email_body);
        
        //Email_template::sendEmail($email_to,$email_data,$body);        
        
  //      Mail::to(request()->email)->send(new newpassword($token));
        
        return (new MailMessage)
                ->from('info@skulocity.com', $email_data->name)
                ->subject($email_data->subject)
                ->view('emails.email_body', ['final_content' => $email_body]);
        
    }
}
