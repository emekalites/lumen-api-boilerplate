<?php

namespace App\Http\Controllers\V1;

use App\Role;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new token.
     *
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() +  env('JWT_EXPIRATION_TIME')
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $user,
            'expires_in' => env('JWT_EXPIRATION_TIME')
        ]);
    }

    /**
     * Authenticate a user and return the token if the provided credentials are correct.
     *
     * @param  Request   $request
     * @return mixed
     */
    public function authenticate(Request $request) {
        try {
            $this->validate($request, [
                'email'     => 'required|email',
                'password'  => 'required'
            ]);

            // Find the user by email
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return response()->json(['error' => 'user does not exist'], 400);
            }

            // Verify the password and generate the token
            if (Hash::check($request->input('password'), $user->password)) {
                $user = $this->respondWithToken($this->jwt($user), $user);

                return response()->json(compact('user'), 200);
            }
            // Bad Request response
            return response()->json(['error' => 'invalid email or password'], 400);
        } catch (\Exception $e){
            error_log($e->getLine().' '.$e->getMessage());
            return response()->json(['error'=> 'could not login user'], 500);
        }
    }

    /**
     * @OA\Post(path="/register",
     *   tags={"users"},
     *   summary="Create user",
     *   description="This is for new users.",
     *   operationId="register",
     *   @OA\RequestBody(
     *       required=true,
     *       description="Created user object",
     *       @OA\MediaType(
     *           mediaType="multipart/x-www-form-urlencoded",
     *           @OA\Schema(ref="#/components/schemas/User")
     *       )
     *   ),
     *   @OA\Response(response="default", description="successful operation")
     * )
     */
    public function register(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $error = (object) null;
            $error->validation = $validator->errors();

            return response()->json(['error'=> $error], 500);
        }

        try {
            $input = $request->all();

            $user = User::create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            if ($user) {
                $user->roles()->attach(Role::where('name', 'user')->first());

                $user->last_login = date('Y-m-d H:i:s');
                $user->save();

                $user = $this->respondWithToken($this->jwt($user), $user);
                return response()->json(compact('user'), 200);
            }

            return response()->json(['error'=> 'could not register user'], 422);

        } catch (\Exception $e){
            error_log($e->getLine().' '.$e->getMessage());
            return response()->json(['error'=> 'could not register user'], 500);
        }
    }

    public function logout(){
        return response()->json(['user' => null], 200);
    }
}
