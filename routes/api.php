<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes (only for learners)
    Route::middleware(['auth:sanctum', 'role:learner'])->group(function () {
        Route::get('/courses', [CourseController::class, 'index']);
        Route::get('/learner/courses/{course_id}/lessons', [LessonController::class, 'index']);
    });

    // Protected routes (only for instructors)
    Route::middleware(['auth:sanctum', 'role:instructor'])->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
        Route::post('/instructor/lessons', [LessonController::class, 'store']);
        Route::put('/instructor/lessons/{id}', [LessonController::class, 'update']);
        Route::delete('/instructor/lessons/{id}', [LessonController::class, 'delete']);
        Route::post('/instructor/materials', [MaterialController::class, 'store']);
    });

    // Protected routes (only for admin)
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::post('/admin/users', [UserController::class, 'store']);
        Route::put('/admin/users/{id}', [UserController::class, 'update']);
        Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
        Route::put('/admin/users/{id}/role', [UserController::class, 'changeUserRole']);
    });

    Route::middleware('auth:sanctum')->get('/test', function (Request $request) {
        return Auth::user();
    });
});
