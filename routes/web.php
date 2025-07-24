<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\RoleMiddleware;

if (!defined('ROLE_ADMIN')) {
    define('ROLE_ADMIN', [App\Http\Middleware\RoleMiddleware::class . ':admin']);
}

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['show']);

    Route::get('/tasks/exportCsv', [TaskController::class, 'exportCsv'])->name('tasks.export');
});

Route::prefix('admin')->group(function () {
    Route::resource('users', UserController::class);
})->middleware(ROLE_ADMIN);


require __DIR__.'/auth.php';
