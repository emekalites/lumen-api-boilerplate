<?php
/**
 * Created by PhpStorm.
 * User: emnity
 * Date: 3/11/19
 * Time: 9:11 PM
 */

namespace App\Http\Middleware;


use App\User;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        //$token = $request->get('token');
        $token = $request->bearerToken();

        if(!$token) {
            return response()->json(['error' => 'token not provided'], 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json(['error' => 'provided token is expired'], 400);
        } catch(Exception $e) {
            return response()->json(['error' => 'an error occured while decoding token'], 400);
        }

        $user = User::find($credentials->sub);
        $request->auth = $user;

        return $next($request);
    }
}