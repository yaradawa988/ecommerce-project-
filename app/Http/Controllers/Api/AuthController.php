<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register new customer",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="P@ssw0rd123"),
     *             @OA\Property(property="password_confirmation", type="string", example="P@ssw0rd123")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required','email:rfc,dns','max:255','unique:users,email'],
            'password' => ['required','confirmed', Rules\Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
        $user->roles()->attach($customerRole);

        $token = $this->createToken($user);

        return response()->json([
            'token' => $token->plainTextToken,
            'user' => $user->load('roles.permissions'),
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="P@ssw0rd123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful login"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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
        $user->tokens()->delete();

       // اجمع صلاحيات المستخدم من roles -> permissions
$abilities = $user->roles
    ->flatMap(fn($role) => $role->permissions->pluck('slug'))
    ->unique()
    ->values()
    ->toArray();

$token = $user->createToken('access-token', $abilities)->plainTextToken;

return response()->json([
    'token' => $token,
    'user' => $user->load('roles.permissions'),
]);}

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logged out")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    private function createToken(User $user)
    {
        $abilities = $user->roles
            ->flatMap(fn($role) => $role->permissions->pluck('slug'))
            ->unique()
            ->toArray();

        return $user->createToken('api-token', $abilities, now()->addDays(7));
    }
}
