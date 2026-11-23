<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/register",
        summary: "Реєстрація нового користувача та отримання Bearer-токена",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Олена Коваленко"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "olena@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Користувача зареєстровано"),
            new OA\Response(response: 422, description: "Помилка валідації")
        ]
    )]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    #[OA\Post(
        path: "/login",
        summary: "Автентифікація користувача та отримання Bearer-токена",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "olena@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Успішний вхід (повертає об'єкт user та token)"),
            new OA\Response(response: 422, description: "Невірні облікові дані")
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Невірний email або пароль.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    #[OA\Post(
        path: "/logout",
        summary: "Вихід із системи (відкликання поточного токена)",
        security: [["bearerAuth" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 200, description: "Вихід виконано успішно"),
            new OA\Response(response: 401, description: "Неавторизований доступ")
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Вихід виконано успішно.']);
    }
}