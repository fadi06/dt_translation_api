<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginFormRequest;
use App\Http\Requests\UserRegisterFormRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Registers a new user and returns user details along with an authentication token.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Registration request payload",
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="Fawad"),
     *             @OA\Property(property="email", type="string", format="email", example="fawad@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="********"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="********")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Fawad"),
     *                 @OA\Property(property="email", type="string", format="email", example="fawad@example.com"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-05T16:41:33.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-05T16:41:33.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="token", type="string", example="1|****************************")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="email", type="string", example="The email field is required."),
     *             )
     *         )
     *     )
     * )
     */
    public function register(UserRegisterFormRequest $request): JsonResponse
    {
        $input = $request->validated();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $tokenResult = $user->createToken(config('app.api_token'));

        $success['token'] = $tokenResult->plainTextToken;
        $success['name'] = $user->name;
        $success['email'] = $user->email;

        return $this->sendSuccess($success, 'User register successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Logs in a user with email and password and returns an auth token.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login request payload",
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="fawad@example1.com"),
     *             @OA\Property(property="password", type="string", format="password", example="********")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="name", type="string", example="Fawad"),
     *                 @OA\Property(property="email", type="string", format="email", example="fawad@example1.com"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example=null),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-29T14:54:14.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-29T14:54:14.000000Z"),
     *                 @OA\Property(property="token", type="string", example="1|****************************")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid email or password",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid email or password")
     *         )
     *     )
     * )
     */
    public function login(UserLoginFormRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $tokenResult = $user->createToken(config('app.api_token'));
            $success['token'] = $tokenResult->plainTextToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;

            return $this->sendSuccess($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="User logout",
     *     description="Logs out the authenticated user.",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Auth token missing or invalid",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendSuccess([], 'User logged out successfully.');
    }
}
