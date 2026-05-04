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
Route::get('/job-offers', [JobOfferController::class, 'index']);

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Rutas comunes
    Route::get('/me', [AuthController::class, 'me']);

    // Comentamos o quitamos el apiResource general si vas a usar las rutas de /company
    // Route::apiResource('job-offers', JobOfferController::class);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/profile/resume', [ProfileController::class, 'resume']);
    Route::apiResource('experiences', ExperienceController::class);
    Route::apiResource('educations', EducationController::class);
    Route::delete('/skills/{id}', [SkillController::class, 'destroy']);
    Route::post('/skills', [ProfileController::class, 'addSkill']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/applications', [JobApplicationController::class, 'store']);

    Route::get('/suggestions', [AuthController::class, 'getSuggestions']);
    Route::post('/messages/interview', [MessageController::class, 'sendInterviewRequest']);

    Route::get('/candidates', [AuthController::class, 'getCandidates']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/messages/my-messages', [MessageController::class, 'getMyMessages']);

    /*
    |--- 1. ROL COMPANY (Empresas) ---
    */
    Route::prefix('company')->group(function () {

        Route::get('/my-offers', [CompanyController::class, 'getMyOffers']);

        // Definimos las rutas de ofertas dentro de 'company' para que coincidan con tu Angular
        Route::post('/job-offers', [JobOfferController::class, 'store']);
        Route::put('/job-offers/{id}', [JobOfferController::class, 'update']);
        Route::delete('/job-offers/{id}', [JobOfferController::class, 'destroy']);
        Route::get('/my-profile', [CompanyController::class, 'getMyProfile']);
        Route::put('/my-profile', [CompanyController::class, 'updateProfile']);
        Route::get('/candidates', [CompanyController::class, 'getCandidates']);
        Route::get('/job-offers/{id}/applications', [JobApplicationController::class, 'getApplicantsByOffer']); // Candidatos de una oferta
    });

    /*
    |--- 2. ROL USER (Candidatos) ---
    | Se mantiene el middleware de habilidades pero sin duplicar rutas de fuera
    */
    Route::prefix('user')->group(function () {
        Route::get('/my-applications', [JobApplicationController::class, 'myApplications']);
        // Route::post('/applications', [JobApplicationController::class, 'store']);
    });

    /*
    |--- 3. ROL ADMIN (SuperAdministrador del Sistema) ---
    */
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getDashboardStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/users', [AdminController::class, 'storeUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);
        Route::get('/offers', [AdminController::class, 'getJobOffers']);
        Route::get('/messages', [AdminController::class, 'getMessages']);
        Route::get('/companies', [AdminController::class, 'getCompanies']);
        Route::get('/my-profile', [AdminController::class, 'getMyProfile']); //MIRARRR
        Route::delete('/admin/messages/{id}', [AdminController::class, 'deleteMessage']);
    });

});
