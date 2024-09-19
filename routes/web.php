<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;

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

Route::get('/', function () {
    return Inertia::render('Welcome2', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile_image', [ProfileController::class, 'updateProfileImage'])->name('update_profile_image');
});

Route::middleware([
    'auth', /* 'verified' */
])
    ->prefix('files')
    ->name('files.')
    ->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('/file/s/{storageItem}/{mode?}', [FileController::class, 'showSigned'])->name('showSigned')->middleware('signed');
        Route::get('/upload', [FileController::class, 'upload'])->name('upload');
        Route::post('/upload', [FileController::class, 'uploadFiles'])->name('upload_process');
        Route::post('/favorite/{hashedid}', [FileController::class, 'toggleFavorite'])->name('toggle_favorite');
        Route::get('/embedded/{embeddedId}', [FileController::class, 'embedded'])->name('embedded');
        Route::get('/render/{hashedid}', [FileController::class, 'renderPdfProtected'])
            ->name('render_pdf_protected');
    });

Route::middleware([
    'auth', /* 'verified' */
])
    ->prefix('wip')
    ->name('wip.')
    ->group(function () {
        Route::get('/files', [FileController::class, 'wipIndex'])->name('index');
    });

require __DIR__ . '/auth.php';
