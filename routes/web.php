<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskExportController;
use App\Http\Controllers\TaskImportController;
use App\Http\Controllers\UserAvatarController;
use Illuminate\Support\Facades\Route;
use Oneofftech\Identities\Facades\Identity;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', 'login');

// Login and register via third party providers

Identity::routes();

// Dashboard -------

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', DashboardController::class)->name('dashboard');

// Avatar ----------

Route::middleware(['auth:sanctum', 'verified'])->get('/avatar-{avatar}.svg', UserAvatarController::class)->name('avatar.show');

// Projects --------

Route::middleware(['auth:sanctum', 'verified'])->get('projects/{project}/report', ReportController::class)->name('projects.report');

Route::middleware(['auth:sanctum', 'verified'])->resource('projects', ProjectController::class);

// Tasks -----------

Route::middleware(['auth:sanctum', 'verified'])->get('tasks/import', [TaskImportController::class, 'create'])->name('tasks.import.create');

Route::middleware(['auth:sanctum', 'verified'])->post('tasks/import', [TaskImportController::class, 'store'])->name('tasks.import.store');

Route::middleware(['auth:sanctum', 'verified'])->get('tasks/export', [TaskExportController::class, 'show'])->name('tasks.export.show');

Route::middleware(['auth:sanctum', 'verified'])->resource('tasks', TaskController::class);
