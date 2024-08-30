<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/files', function () {
    $fakeItem = function (array $data = []) {
        $name = $data['name'] ?? null;
        $label = $data['label'] ?? $name ?? null;
        $name = is_string($name) ? trim($name) : ucfirst(fake()->word());
        $label = is_string($label) ? trim($label) : $name;

        $type = match ($data['type'] ?? 'file') {
            'file' => 'file',
            'dir' => 'dir',
            default => 'file',
        };

        $isDir = $type === 'dir';
        $username = 'user01';
        $id = str()->uuid()->toString();

        return [
            'hashid' => hashids_encode_hex($id),
            'base_path' => '/', //"/{$username}",
            'owner_username' => $username,
            'type' => $type,
            'color' => $isDir ? Arr::random(['red', 'blue', 'green', null]) : null, // dir only
            'name' => $name,
            'label' => $label,
            'favorite' => boolval($data['favorite'] ?? false),
            'updated_at' => now()->subDays(4),
            'updated_at_humans_label' => now()->subDays(4)->diffForHumans(),
            'mime_type' => $isDir ? null : 'application/pdf', // file only
            'size' => $isDir ? null : 1458, // file only
            'size_humans_label' => $isDir ? null : '1.45MB', // file only
        ];
    };

    $items = collect([
        $fakeItem(['type' => 'dir', 'name' => 'School', 'favorite' => true]),
        $fakeItem(['type' => 'file', 'name' => 'file02.pdf']),
        $fakeItem(['type' => 'file', 'name' => 'file05.pdf']),
        $fakeItem(['type' => 'file', 'name' => 'file01.pdf', 'favorite' => true]),
        $fakeItem(['type' => 'file', 'name' => 'file04.pdf']),
        $fakeItem(['type' => 'file', 'name' => 'file03.pdf']),
        $fakeItem(['type' => 'dir']),
    ])
        // ->sortBy(fn(array $item) => $item['type'] === 'dir' ? -1 : 1) // Good
        ->sortBy('name')
        ->sortBy(fn (array $item) => ($item['size'] ?? 0))
        ->sortBy(fn (array $item) => match ($item['type']) {
            'dir' => -1, 'file' => 1, default => 2
        }) // Advanced
        ->values();

    return Inertia::render('Files/Index', [
        'items' => $items,
    ]);
})->middleware(['auth', 'verified'])->name('files.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
