<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name("user.index"); // List
    Route::get('/search/{identifier}', [UserController::class, 'show'])->name("user.show"); // Read
    Route::get('/create', [UserController::class, 'create'])->name("user.create"); // Create
    Route::post('/create', [UserController::class, 'store'])->name("user.store"); // store
    Route::get('/{identifier}', [UserController::class, 'show'])->name("user.show"); // Read
    Route::put('/update/{id}', [UserController::class, 'update'])->name("user.update");  // Update
    Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name("user.delete"); // Delete
});