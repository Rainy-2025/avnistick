<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    // Send OTP for email verification
    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|unique:email_verifications,email',
            'password' => 'required|string|min:6',
        ]);

        $otp = rand(100000, 999999);

        DB::table('email_verifications')->updateOrInsert(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.'
        ], 200);
    }

    // Verify OTP and register user
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $record = DB::table('email_verifications')->where('email', $request->email)->first();

        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'No OTP found for this email.'
            ], 404);
        }

        if ((string)$record->otp !== (string)$request->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP.'
            ], 400);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired.'
            ], 400);
        }

        $user = User::create([
            'name' => $record->name,
            'email' => $record->email,
            'password' => $record->password,
        ]);

        DB::table('email_verifications')->where('email', $record->email)->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Email verified and user registered.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    // Login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    // Get authenticated user profile
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ], 200);
    }

    // Logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful.'
        ], 200);
    }
}
