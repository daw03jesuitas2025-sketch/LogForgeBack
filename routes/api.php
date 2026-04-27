<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController; // Nuevo
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

    // Autenticación y Usuario
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- SECCIÓN PARA EMPRESAS (Dashboard de Empresa) ---
    Route::prefix('company')->group(function () {
        Route::get('/my-profile', [CompanyController::class, 'getMyProfile']);
        Route::put('/my-profile', [CompanyController::class, 'updateProfile']);
        Route::get('/my-offers', [CompanyController::class, 'getMyOffers']);
        Route::get('/candidates', [CompanyController::class, 'getCandidates']);

        // CRUD de ofertas desde la perspectiva de la empresa
        Route::apiResource('job-offers', JobOfferController::class);
    });

    // --- SECCIÓN PARA CANDIDATOS (Perfil y Postulaciones) ---
    Route::get('/profile/resume', [ProfileController::class, 'downloadResume']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/skills', [ProfileController::class, 'addSkill']);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('educations', EducationController::class);

    Route::post('/applications', [JobApplicationController::class, 'store']);
    Route::get('/my-applications', [JobApplicationController::class, 'myApplications']);
    Route::get('/suggestions', [AuthController::class, 'getSuggestions']);

    // --- MENSAJERÍA E INTERVENCIONES ---
    Route::post('/messages/interview', [MessageController::class, 'sendInterviewRequest']);
    Route::get('/messages/my-messages', [MessageController::class, 'getMyMessages']);

    // --- RUTAS DE ADMINISTRACIÓN GLOBAL ---
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/offers', [AdminController::class, 'getJobOffers']);
        Route::get('/messages', [AdminController::class, 'getMessages']);
        Route::get('/companies', [AdminController::class, 'getCompanies']);
        Route::get('/my-profile', [AdminController::class, 'getMyProfile']); // Perfil del admin si fuera necesario
    });

    // Ruta general para ver ofertas (usada por candidatos)
    Route::get('/job-offers-list', [JobOfferController::class, 'index']);
});
