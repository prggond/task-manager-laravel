<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
  use ApiResponse;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // ✅ validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        // ❌ wrong credentials
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // ✅ success
        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    

   
}