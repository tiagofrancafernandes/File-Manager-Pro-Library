<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Storage;

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
    $redirectTo = route('files.index');

    if ($redirectTo) {
        return redirect($redirectTo);
    }

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
        Route::get('/{hashedid}/download', [FileController::class, 'download'])->name('download');
        Route::delete('/{hashedid}/destroy', [FileController::class, 'destroy'])->name('destroy');
    });

Route::middleware([
    //
])
    ->prefix('reader')
    ->name('reader.')
    ->group(function () {
        Route::get('/embedded/{embeddedId}', [FileController::class, 'embedded'])->name('embedded');
        Route::get('/render/{hashedid}', [FileController::class, 'renderPdfProtected'])->name('render');
        Route::get('/render/{hashedid}/bdata', [FileController::class, 'b64EncodedPdf'])->name('b64_encoded_pdf');
    });

Route::middleware([
    'auth', /* 'verified' */
])
    ->prefix('wip')
    ->name('wip.')
    ->group(function () {
        Route::get('/files', [FileController::class, 'wipIndex'])->name('index');

        Route::get('/wip-base64-file', function () {
            $filePath = Storage::disk('public')->path('uploads/brunno.pdf');

            // return response()->file($filePath);

            $pdfFile = $filePath;

            if (!is_file($pdfFile)) {
                http_response_code(404);

                die('Not found!');
            }

            $b64Data = base64_encode(file_get_contents($pdfFile));

            $salt = 't8ggh';
            $salt = ''; // Ajuda a proteger o PDF contra decode indevido

            die(implode('', [$salt, $b64Data, $salt]));
        })->name('base64_file');

        Route::get('/wip-base64-render', fn () => view('pdfjs.render-v1'))->name('base64_render');
    });

require __DIR__ . '/auth.php';
