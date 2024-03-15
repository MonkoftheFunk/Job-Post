<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers;

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

// frontend
Route::get('/', [Controllers\ListingController::class,'index'])
    ->name('listings.index');
Route::get('/l/{listing}', [Controllers\ListingController::class,'show'])
    ->name('listings.show');
Route::get('/l/{listing}/apply', [Controllers\ListingController::class, 'apply'])
    ->name('listings.apply');

// backend
Route::get('/a/new', [Controllers\ListingController::class, 'create'])
    ->name('admin.listings.create');
Route::post('/a/new', [Controllers\ListingController::class, 'store'])
    ->name('admin.listings.store');

Route::get('/a/dashboard',[Controllers\ListingController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
Route::get('/a/{listing}/edit', [Controllers\ListingController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('admin.listings.edit');
Route::put('/a/{listing}/update', [Controllers\ListingController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('admin.listings.update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
