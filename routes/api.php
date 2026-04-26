<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/job-offers', [JobOfferController::class, 'index']); // Ver todas las ofertas en el inicio

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Rutas comunes para cualquier usuario autenticado
    Route::get('/me', [AuthController::class, 'me']);
<<<<<<< HEAD

    Route::apiResource('job-offers', JobOfferController::class);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/profile/resume', [ProfileController::class, 'resume']);
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
=======
    Route::post('/logout', [AuthController::class, 'logout']);
>>>>>>> temp-fix
    Route::get('/messages/my-messages', [MessageController::class, 'getMyMessages']);

    /*
    |--- 1. ROL COMPANY (Empresas) ---
    */
    Route::prefix('company')->group(function () {

        Route::get('/my-offers', [CompanyController::class, 'getMyOffers']);

        Route::get('/my-profile', [CompanyController::class, 'getMyProfile']);
        Route::put('/my-profile', [CompanyController::class, 'updateProfile']);

        Route::get('/candidates', [CompanyController::class, 'getCandidates']);

        // Reclutamiento
        Route::get('/candidates', [CompanyController::class, 'getCandidates']);
        Route::post('/messages/interview', [MessageController::class, 'sendInterviewRequest']);
    });

    /*
    |--- 2. ROL USER (Candidatos) ---
    */
    Route::middleware('ability:role-user')->group(function () {
        // Postulaciones y sugerencias
        Route::post('/applications', [JobApplicationController::class, 'store']);
        Route::get('/my-applications', [JobApplicationController::class, 'myApplications']);
        Route::get('/suggestions', [AuthController::class, 'getSuggestions']);

        // Perfil personal y CV del candidato
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::get('/profile/resume', [ProfileController::class, 'resume']);

        Route::apiResource('experiences', ExperienceController::class);
        Route::apiResource('educations', EducationController::class);
        Route::post('/skills', [ProfileController::class, 'addSkill']);
        Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
    });

    /*
    |--- 3. ROL ADMIN (SuperAdministrador del Sistema) ---
    */
    Route::prefix('admin')->middleware('ability:role-admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/offers', [AdminController::class, 'getJobOffers']); // Ver TODAS las ofertas de todas las empresas
        Route::get('/messages', [AdminController::class, 'getMessages']);
        Route::get('/companies', [AdminController::class, 'getCompanies']);
        Route::get('/my-profile', [AdminController::class, 'getMyProfile']); // Perfil administrativo
    });

});
