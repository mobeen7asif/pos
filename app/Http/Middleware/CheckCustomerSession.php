<?php
namespace App\Http\Middleware;
use App\Customer;
use App\CustomerSession;
use App\UserSession;
use Closure;
//use App\LoginUsers;
use Illuminate\Support\Facades\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
class CheckCustomerSession {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $headers = getallheaders();
        if(isset($headers['Authorization'])) {
            $checksession = CustomerSession::where('session_token', $headers['Authorization'])->first();
            if ($checksession) {
                $user = Customer::find($checksession->user_id);
                return $next($request);
            } else {
                return response()->json(['message' => 'Unauthenticated'], 401);
                //return Response::json(array('status' => 'error', 'errorMessage' => 'Session Expired', 'errorCode' => 400), 400);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }
}