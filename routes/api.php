<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
Route::get('/tasks/report', [TaskController::class, 'report']);