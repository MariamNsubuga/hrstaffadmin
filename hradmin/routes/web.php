<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Admin login routes
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');

// Protected admin routes
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/audits', [AdminController::class, 'index'])->name('admin.audits');
    // Show the form to add a new admin
Route::get('/admin/add', [AdminController::class, 'create'])->name('admin.create')->middleware('auth');

// Handle the form submission
Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store')->middleware('auth');

});

// Authentication routes
Auth::routes(); // This line registers the login route, among others.

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Define the logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');