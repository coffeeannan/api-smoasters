<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ValidationException;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users'],
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('annan')->plainTextToken;
        return response()->json([
            'name' => $user['name'],
            'email' => $user['email'],
            'access_token' =>  $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'These credentials do not match our records.'], 400);
        }


        $token = $user->createToken('annan')->plainTextToken;
        return response()->json([
            'name' => $user['name'],
            'email' => $user['email'],
            'access_token' =>  $token,
        ], 200);
    }

    public function signout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Personal access token was revoked.'], 200);
    }
}
