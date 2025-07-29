<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    // Admin login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = $admin->createToken('admin-token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'data' => [
                    'token' => $token,
                    'admin' => $admin
                ]
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password.',
            'data' => null
        ], 401);
    }

    // Admin logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful.',
            'data' => null
        ], 200);
    }

    // Admin profile
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully.',
            'data' => $request->user()
        ], 200);
    }
}
