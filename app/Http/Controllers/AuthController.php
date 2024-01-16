<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['none']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => __('error.401')], 401);
        } else {
            if (auth()->user()->is_active) {
                Log::info(__('notification.login', ['name' => auth()->user()->name ]));
                return $this->respondWithToken($token);
            } else {
                return response()->json(['error' => __('error.403')], 403);
            }
        }
    }

    /*
     * Get a Sanctum API via given credentials.
     */
    public function login_sanctum()
    {
        $validate = Validator::make($request->all(), [
            "email"         => "bail|required|email",
            "password"      => "bail|required|min:4|max:256",
        ]);
    
        if ($validate->fails()) {
            return response()->json([
                "msg" => __('paw.errorsfound'),
                "errors" =>$validate->errors()
            ], 422);
        }
     
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => __('error.401')], 401);
        }
        $token = $user->createToken("login")->plainTextToken;

        $user->notify(new LoginSuccessful(
            __('notification.login', ['name' => auth()->user()->name ]),
            __('notification.header')));
        return response()->json([
            "msg"   => __('auth.token'),
            "token" => $token
        ], 201);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
                    "msg" => __('paw.userfound'),
                    "data" => auth()->user(),
                ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['msg' => __('logout')]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' =>  Auth::factory()->getTTL() * 60
        ]);
    }
}
