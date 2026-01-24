<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
//use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
           
        ]);

         // Assign default role: customer
        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
        $user->roles()->attach($customerRole);

        // Generate token with abilities
        $token = $this->createToken($user);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user->load('roles.permissions'),
        ], 201);
    }


        // ================= Login =================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Delete previous tokens
        $user->tokens()->delete();

        $token = $this->createToken($user);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user->load('roles.permissions'),
        ]);
    }


       public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

 private function createToken(User $user)
    {
        // Gather abilities from all roles
        $abilities = $user->roles
            ->flatMap(fn($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->toArray();

        return $user->createToken(
            'api-token',
            $abilities,
            now()->addDays(7) // Expire in 7 days
        );
    }



    
 

}