<?php
namespace App\Http\Middleware;
use App\Customer;
use App\CustomerSession;
use App\UserSession;
use Closure;
//use App\LoginUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\User;
use Illuminate\Support\Facades\Auth;
class CheckErpToken {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $headers = getallheaders();
        if(isset($headers['user_name']) && $headers['token']) {
            $checksession = DB::table('erp_tokens')->where(['user_name' => 'user_skulocity','token' => 'test_token'])->first();
            if ($checksession) {
                return $next($request);
            } else {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        }else {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
    }
}