<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $admin = Admin::where('email', $request->email)->first();

    if ($admin && Hash::check($request->password, $admin->password)) {
        // Create token
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'admin' => $admin
        ]);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Invalid email or password'
    ], 401);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Logout successful',
    ]);
}
public function profile(Request $request)
{
    return response()->json([
        'admin' => $request->user() // will return authenticated Admin
    ]);
}

}


