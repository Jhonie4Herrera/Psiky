<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'estandar',
            'gender' => $request->gender,
        ]);

        if ($request->role === 'profesional') {
            $user->professional()->create($request->only('especialidad', 'age_range'));
            $user->update(['approved' => false]); // Pending admin approval
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Registered. Verify email.']);
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            return response()->json(['token' => $user->createToken('api')->plainTextToken, 'user' => $user]);
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::firstOrCreate(['email' => $googleUser->email], [
            'name' => $googleUser->name,
            'role' => 'estandar',
            'google_id' => $googleUser->id,
        ]);
        return response()->json(['token' => $user->createToken('api')->plainTextToken]);
    }
}