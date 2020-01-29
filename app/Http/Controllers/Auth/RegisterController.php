<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules['first_name'] = 'required|string|max:255';        
        $rules['last_name'] = 'required|string|max:255';        
        $rules['name'] = 'required|string|max:255';        
        $rules['location'] = 'required';        
        $rules['location_visible'] = 'required';        
        $rules['password'] = 'required|string|min:6|confirmed';
        $rules['g-recaptcha-response'] = 'required|captcha';
        
        if($data['email']){
          $user = User::where('email',$data['email'])->first();
            if($user){
                if($user->password == NULL){
                   $rules['email'] = 'required|string|email|max:255'; 
                }else{
                   $rules['email'] = 'required|string|email|max:255|unique:users';
                }
            }else{
              $rules['email'] = 'required|string|email|max:255|unique:users';  
            }  
        }else{
            $rules['email'] = 'required|string|email|max:255|unique:users';  
        }
        
        
        
        
        $validation =  Validator::make($data, $rules);
         
         return $validation;
    }
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $userdata['email'] = $data['email'];
        
        $user = User::firstOrNew($userdata);
        
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->name = $data['name'];
        $user->user_country = $data['location'];
        $user->location_visible = $data['location_visible'];
        $user->password = bcrypt($data['password']);
        $user->save();
        
        return $user;
    }
}
