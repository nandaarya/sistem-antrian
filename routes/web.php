<?php

use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return 'API Sistem Antrian Siap Untuk Digunakan!';
});

Route::post('/login', [AdminController::class, 'login']);
Route::post('/start-queue', [QueueController::class, 'startQueue']);
Route::post('/queues/take', [QueueController::class, 'takeQueue']);
Route::get('/queues', [QueueController::class, 'queueList']);
Route::post('/queues/next', [QueueController::class, 'next']);
Route::post('/queues/prev', [QueueController::class, 'prev']);