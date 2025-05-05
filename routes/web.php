<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FolderController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

    // File routes
    Route::get('/', [App\Http\Controllers\FileController::class, 'index'])->name('dashboard');
    // Route::get('/files', [App\Http\Controllers\FileController::class, 'index'])->name('files.index');
    Route::post('/upload', [App\Http\Controllers\FileController::class, 'upload'])->name('upload');
    Route::get('/download/{file}', [App\Http\Controllers\FileController::class, 'download'])->name('download');
    Route::delete('/delete/{file}', [App\Http\Controllers\FileController::class, 'delete'])->name('delete');
    Route::get('/files/{file}/share', [App\Http\Controllers\FileController::class, 'share'])->name('files.share');
    Route::post('/files/{file}/share', [App\Http\Controllers\FileController::class, 'storeShare'])->name('files.storeShare');
    Route::delete('/files/{file}/share/{share}', [App\Http\Controllers\FileController::class, 'unshare'])->name('files.unshare');
    Route::get('/files/{file}/actions', [App\Http\Controllers\FileController::class, 'actions'])->name('files.actions');
    Route::post('/files/{file}/copy', [App\Http\Controllers\FileController::class, 'copy'])->name('files.copy');
    Route::post('/files/{file}/move', [App\Http\Controllers\FileController::class, 'move'])->name('files.move');

    // Folder routes
    Route::post('/folders', [App\Http\Controllers\FolderController::class, 'store'])->name('folders.store');
    Route::get('/folders/{folder}', [App\Http\Controllers\FolderController::class, 'show'])->name('folders.show');
    Route::delete('/folders/{folder}', [App\Http\Controllers\FolderController::class, 'destroy'])->name('folders.destroy');
    Route::get('/folders/{folder}/share', [App\Http\Controllers\FolderController::class, 'share'])->name('folders.share');
    Route::post('/folders/{folder}/share', [App\Http\Controllers\FolderController::class, 'storeShare'])->name('folders.storeShare');
    Route::delete('/folders/{folder}/share/{share}', [App\Http\Controllers\FolderController::class, 'unshare'])->name('folders.unshare');
    Route::get('/folders/{folder}/copy', [App\Http\Controllers\FolderController::class, 'copy'])->name('folders.copy');
    Route::post('/folders/{folder}/copy', [App\Http\Controllers\FolderController::class, 'storeCopy'])->name('folders.storeCopy');
    Route::get('/folders/{folder}/move', [App\Http\Controllers\FolderController::class, 'move'])->name('folders.move');
    Route::post('/folders/{folder}/move', [App\Http\Controllers\FolderController::class, 'storeMove'])->name('folders.storeMove');
});

require __DIR__.'/auth.php';
