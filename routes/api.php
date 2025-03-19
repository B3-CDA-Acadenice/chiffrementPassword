<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\RoleMiddleware; 
use App\Http\Controllers\API\RoleController;

// Routes protégées pour les administrateurs (middleware 'auth:sanctum' et 'role:admin')
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Routes d'authentification : Inscription et Connexion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par 'auth:sanctum' pour les utilisateurs authentifiés
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource("users", UserController::class); // CRUD pour les utilisateurs
    Route::get('/roles', [UserController::class, 'getRoles']); 
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']); // ✅ Ajout d'une route spécifique pour modifier le rôle
    Route::put('/users/{id}', [UserController::class, 'update']); // ✅ Route pour modifier un utilisateur sans changer le rôle
    Route::get('/user/me', [UserController::class, 'getUser']); // Permet à l'utilisateur de voir son propre profil

});
