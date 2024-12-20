<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Job;
use Illuminate\Container\Attributes\Auth;



Route::get('/', [JobController::class, 'index']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/jobs', function () {
    return Job::with(['employer', 'tags'])->get();
});

Route::get('/jobs/{job}', [JobController::class, 'show']);

Route::post('/jobs', [JobController::class, 'store'])->middleware('auth:sanctum');

Route::patch('/jobs/{job}', [JobController::class, 'update'])->middleware('auth:sanctum')->can('edit-job', 'job');

Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->middleware('auth:sanctum')->can('edit-job', 'job');

Route::get('/jobs/search/{name}', [SearchController::class, 'search']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
