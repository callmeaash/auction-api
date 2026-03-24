<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

class SessionsController extends Controller
{

    /**
     * Login a user
     *
     * @unauthenticated
     *
     * @param LoginRequest $request Validated user login data.
     * @return JsonResponse Returns authenticated user and their authentication token.
    */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->success([
            'user' => new UserResource($user),
            'token' => $token
        ], 'User logged in successfully');
    }

    /**
     * Logout a user
     *
     * @authenticated
     *
     * @param Request $request Request data.
     * @return JsonResponse Returns success message.
    */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success([], 'User logged out successfully');
    }
}
