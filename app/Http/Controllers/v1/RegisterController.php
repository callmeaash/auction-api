<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;


class RegisterController extends Controller
{
    /**
     * Register a new User.
     * 
     * Creates a new user account and returns an authentication token.
     *
     * @unauthenticated
     * 
     * @param RegisterRequest $request Validated user registration data.
     * @return JsonResponse Returns newly created user and their authentication token.
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->created([
            'user' => new UserResource($user),
            'token' => $token
        ], 'User registered successfully');
    }
}
