<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::post('/login',[AuthController::class,'login']);


Route::middleware('auth:sanctum')->group(function(){
    Route::get('/users', [UserController::class, 'index'])->middleware('role:admin');

    Route::get('/projects',[ProjectController::class,'index']);
    Route::post('/projects',[ProjectController::class,'store']);

    Route::get('/tasks',[TaskController::class,'index']);
    Route::post('/tasks',[TaskController::class,'store']);
    Route::put('/tasks/{id}',[TaskController::class,'update']);
    Route::get('/my-tasks', [TaskController::class, 'myTasks']);
    Route::get('/projects/{id}/tasks', [TaskController::class, 'tasksByProject']);
    Route::post('/projects/{id}/tasks', [TaskController::class, 'store']);

});