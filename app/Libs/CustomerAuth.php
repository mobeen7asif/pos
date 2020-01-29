<?php
namespace App\Libs;
use App\Customer;
use App\CustomerSession;
use App\User;
use Illuminate\Support\Facades\Hash;
class CustomerAuth
{
    /**
     * @param array $credentials
     * @return bool
     */
    public static function attempt(array $credentials)
    {
        try{
            $user = Customer::where('email',$credentials['email'])->first();
            if(!$user){
                return false;
            }
        }
        catch (\Exception $e){
            return false;
        }
        if(!Hash::check($credentials['password'], $user->password))
            return false;
        return true;
    }
    /**
     * @param User $authenticatedUser
     * @return User
     */
    public static function login(User $authenticatedUser,$device_id){
        $authenticatedUser->session_id = bcrypt($authenticatedUser->id);
        $authenticatedUser->device_id = $device_id;
        $authenticatedUser->save();
        return $authenticatedUser;
    }
    public static function logout(User $authenticatedUser = null)
    {
        $authenticatedUser->session_id = "";
        $authenticatedUser->save();
        return true;
    }
    public static function authenticateWithToken($token)
    {
        return ((new UsersRepository())->findByToken($token) == null)?false:true;
    }
    /**
     * @return User $user
     * */
    public static function customer()
    {
        if(isset(getallheaders()['Authorization']) && getallheaders()['Authorization'] != ""){
            $session = CustomerSession::where('session_token',getallheaders()['Authorization'])->first();
            if($session){
                $customer = Customer::find($session->customer_id);
                $customer->session_token = $session->session_token;
                $customer->device_id = $session->device_id;
                $customer->device_type = $session->device_type;
                return $customer;
            } else {
                return null;
            }
        }
        return null;
    }
    public static function check()
    {
        return (Auth::user() != null);
    }
}