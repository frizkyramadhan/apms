<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dmbd\DailyMonitoringController;
use App\Http\Controllers\Dmbd\DashboardController;
use App\Http\Controllers\Dmbd\HistoryController;
use App\Http\Controllers\Dmbd\UnitController;
use App\Http\Controllers\language\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('lang/{locale}', [LanguageController::class, 'swap']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dmbd-dashboard');
    Route::get('/dmbd/daily', DailyMonitoringController::class)->name('dmbd-daily');
    Route::get('/dmbd/history', HistoryController::class)->name('dmbd-history');
    Route::get('/dmbd/units', [UnitController::class, 'index'])->name('dmbd-units');

    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::post('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', UserController::class)->except(['create']);

    Route::resource('roles', RoleController::class)->except(['create', 'edit']);

    Route::get('permissions/data', [PermissionController::class, 'data'])->name('permissions.data');
    Route::resource('permissions', PermissionController::class)->except(['create', 'edit']);
});
