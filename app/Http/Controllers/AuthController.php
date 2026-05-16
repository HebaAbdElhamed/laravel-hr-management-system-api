<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    //Register

    public function register(Request $request)
    {
        $fields_Validation = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => ['required', Password::min(8)->letters()->numbers()->symbols()]
        ]);

        try {
            $user = User::create([
                'name' => $fields_Validation['name'],
                'email' => $fields_Validation['email'],
                'password' => Hash::make($fields_Validation['password']),
                'role' => 'employee'
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    //Login
    public function login(Request $request)
    {
        $fields_Validation = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields_Validation['email'])->first();
        if (!$user || !Hash::check($fields_Validation['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        try {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    //Logout
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    //userDetails
    public function userDetails(Request $request){
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
