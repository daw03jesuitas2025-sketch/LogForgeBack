<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\MessageController;
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
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
    Route::post('/skills', [ProfileController::class, 'addSkill']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/applications', [JobApplicationController::class, 'store']);
    Route::get('/my-applications', [JobApplicationController::class, 'myApplications']);
    Route::middleware('auth:sanctum')->get('/suggestions', [AuthController::class, 'getSuggestions']);
    Route::post('/messages/interview', [MessageController::class, 'sendInterviewRequest']);
    Route::middleware('auth:sanctum')->get('/candidates', [AuthController::class, 'getCandidates']);
    Route::get('/messages/my-messages', [MessageController::class, 'getMyMessages']);

});

// RUTAS DE ADMINISTRACIÓN
Route::prefix('admin')->group(function () {
    Route::get('/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/offers', [AdminController::class, 'getJobOffers']);
    Route::get('/messages', [AdminController::class, 'getMessages']);
    Route::get('/companies', [AdminController::class, 'getCompanies']);
});
