<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\EducationController;
use Illuminate\Support\Facades\Route;

// RUTAS PÚBLICAS
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// RUTAS PROTEGIDAS POR TOKEN
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('job-offers', JobOfferController::class);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('educations', EducationController::class);
    Route::post('/skills', [ProfileController::class, 'addSkill']);
});

// RUTAS DE ADMINISTRACIÓN
Route::prefix('admin')->group(function () {
    Route::get('/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/offers', [AdminController::class, 'getJobOffers']);
});
