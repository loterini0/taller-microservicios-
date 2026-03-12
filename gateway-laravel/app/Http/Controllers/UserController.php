<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->question = $request->question;
        $user->answer = $request->answer;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'message' => 'User registered successfully'
        ]);
    }

    public function login(Request $request)
    {   
    $credentials = $request->only('email', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer'
    ]);
    }

    public function logout()
    {
    auth()->logout();

    return response()->json([
        'message' => 'Logged out successfully'
    ]);
    }   

    public function password_reset(Request $request)
    {
        $user = User::where('question',$request->question)
                    ->where('answer',$request->answer)
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Incorrect security question or answer'
            ],401);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
}