<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\User;
use App\User_providers;
use Socialite;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }
    
    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $facebook_data = Socialite::driver('facebook')->user();                        
        
        $facebook_user_data['email'] = $facebook_data->email;

        $facebook_user = User::firstOrNew($facebook_user_data);
        
        if(!$facebook_user->exists){
            $full_name = explode(" ", $facebook_data->name);
            $facebook_user->first_name = $full_name[0];
            $facebook_user->last_name = $full_name[1];
            $facebook_user->name = $facebook_data->name;
        }
        
        $facebook_user->save();                
               
        $image_name = $facebook_user->id.'_'.str_random(10).'.png';
        
        if($facebook_user){                        
            $user_provider_data['user_id'] = $facebook_user->id;
            
            $user_provider = User_providers::firstOrNew($user_provider_data);
            $user_provider->token = $facebook_data->token;
            $user_provider->provider_id = $facebook_data->id;
            $user_provider->provider_name = 'fb';
            $user_provider->active = $facebook_data->user['verified'];
            
            $user_provider->update();
            
            $facebook_user->profile_image = $image_name;        
            $facebook_user->update();

            copy($facebook_data->avatar, public_path('/uploads/users/thumbs/'.$image_name));
            copy($facebook_data->avatar_original, public_path('/uploads/users/'.$image_name));
        }
        
        Auth::loginUsingId($facebook_user->id);
                
        return redirect($this->redirectTo);
    }
}
