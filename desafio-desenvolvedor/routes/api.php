<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\FileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Credenciais inválidas.'
        ], 401);
    }

    // limpa tokens antigos aqui
    $token = $user->tokens()->delete();
    $token = $user->createToken('token-desafio')->plainTextToken;

    return response()->json([
        'message' => 'Login realizado com sucesso.',
        'token' => $token
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [FileController::class, 'upload']);
});
