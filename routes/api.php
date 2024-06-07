<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
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

# Register user endpoint
Route::post('register', [AuthController::class, 'register']);

# Login user endpoint
Route::post('login', [AuthController::class, 'login']);

# Protect the routes
Route::middleware('auth:api')->group(function () {
    # Logout endpoint
    Route::post('logout', [AuthController::class, 'logout']);    

    # List tasks endpoint
    Route::get('/tasks', [TaskController::class, 'index']);

    # Create tasks endpoint
    Route::post('/tasks', [TaskController::class, 'store']);

    # Show task endpoint
    Route::get('/tasks/{id}', [TaskController::class, 'show']);

    # Update task endpoint
    Route::put('/tasks/{id}', [TaskController::class, 'update']);

    # Delete task endpoint
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

    # Filter by status endpoint
    Route::get('/tasks/filter/status/{status}', [TaskController::class, 'filterByStatus']);

    # Filter by due date endpoint
    Route::get('/tasks/filter/due-date/{due_date}', [TaskController::class, 'filterByDueDate']);
});
