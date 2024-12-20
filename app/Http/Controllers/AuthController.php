<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\File;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {

            Log::info($request->all());
            $userAttributes = $request->validate([
                'email' => ['required', 'email', 'unique:users,email'],
                'name' => ['required'],
                'password' => ['required', 'confirmed']
            ]);
            $employerAttributes = $request->validate([
                'employer' => ['required'],
                // 'logo' => ['required', File::types(['png', 'jpg', 'webp', 'svg', 'jpeg'])],
            ]);

            $userAttributes['password'] = Hash::make($userAttributes['password']);

            $user = User::create($userAttributes);

            $logoPath = $request->logo?->store('logos') || null;

            // $logoPath = $request->file('logo')->store('logos');

            $user->employer()->create([
                'name' => $employerAttributes['employer'],
                'logo' => $logoPath || null
            ]);
            //     $user->employer()->create(['name'=> $employerAttributes['employer'],
            // 'logo'=>'']);


            // Auth::login($user);

            // return redirect('/');
            $token = $user->createToken($request->name);

            return response()->json(['status' => 'success', 'user' => $user, 'token' => $token->plainTextToken], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        try {

            request()->validate([
                // 'email' => ['required', 'email'],
                'email' => 'required|email|exists:users',
                'password' => ['required'],
            ]);

            //    if(!Auth::attempt($attributes)){
            //     // throw ValidationException::withMessages(['email'=>'Sorry, those credentials do not match.']);
            //     return response()->json(['message'=>'Sorry, those credentials do not match.'], 401);
            //    }

            //    request()->session()->regenerate();
            //    return response()->json(['message'=>'You are now logged in.'], 200);
            //    return redirect('/');

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ['error' => 'Sorry, the provided credentials do not match.'];
            }

            $token = $user->createToken($user->name);

            return response()->json(['status' => 'success', 'user' => $user, 'token' => $token->plainTextToken], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(Request $request)
    {
        try {

            // Auth::logout();
            Log::info('User attempting logout: ', ['user' => $request->user()]);
            Log::info($request->all());

            // return redirect('/');
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Logged Out!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
