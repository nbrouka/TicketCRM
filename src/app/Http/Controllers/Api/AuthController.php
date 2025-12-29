<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Authentication')]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', example: '2023-12-25T10:00:00.00000Z'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-12-25T10:0:00.0000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-12-25T10:00:00.0000Z'),
    ],
    type: 'object'
)]
class AuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register a new user and return API token.
     */
    #[OA\Post(
        path: '/api/register',
        summary: 'Register a new user',
        description: 'Registers a new user and returns an API token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['name', 'email', 'password', 'password_confirmation'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe', description: 'User name'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com', description: 'User email'),
                        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123', description: 'User password'),
                        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123', description: 'Password confirmation'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'User registered successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
                            new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                            new OA\Property(property: 'token', type: 'string', example: '1|exampleapitoken1234567890abcdef', description: 'API token for authentication'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->userService->createApiUserWithToken([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], Response::HTTP_CREATED);
    }

    /**
     * Authenticate user and return API token.
     */
    #[OA\Post(
        path: '/api/login',
        summary: 'Login user',
        description: 'Authenticates user and returns an API token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['email', 'password'],
                    properties: [
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com', description: 'User email'),
                        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123', description: 'User password'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User logged in successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'User logged in successfully'),
                            new OA\Property(property: 'user', ref: '#/components/schemas/User'),
                            new OA\Property(property: 'token', type: 'string', example: '1|exampleapitoken1234567890abcdef', description: 'API token for authentication'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Invalid credentials'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Validation error'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->userService->authenticateApiUserWithToken($request->email, $request->password);

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], Response::HTTP_OK);
    }

    /**
     * Logout user by revoking the current token.
     */
    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout user',
        description: 'Logs out the authenticated user by revoking the current token',
        tags: ['Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'User logged out successfully',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'message', type: 'string', example: 'User logged out successfully'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User logged out successfully',
        ], Response::HTTP_OK);
    }

    /**
     * Get the authenticated user.
     */
    #[OA\Get(
        path: '/api/user',
        summary: 'Get authenticated user',
        description: 'Returns the authenticated user details',
        tags: ['Authentication'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Authenticated user details',
                content: new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                            new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', example: '2023-12-25T10:00:00.00000Z'),
                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-12-25T10:00:00.000000Z'),
                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-12-25T10:00:00.0000Z'),
                        ]
                    )
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: 'Unauthorized'),
        ]
    )]
    public function user(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
