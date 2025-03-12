<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();

    //         return response()->json(['message' => 'You are now logged in.']);
    //     }

    //     return response()->json(['message' => 'Invalid credentials.'], 401);
    // }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        $token =$user->createToken($request->name);
        return response()->json(['token' => $token]);
    }
    // public function logout(Request $request)
    // {
    //     Auth::logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     return response()->json(['message' => 'You are now logged out.']);
    // }
    public function login(Request $request)
    {
        
        return 'login';
    }
    // public function register(Request $request)
    // {
    //     return 'register';
    // }
    public function logout(Request $request)
    {
        return 'logout';
    }
}
