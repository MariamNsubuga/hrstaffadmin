<?php

use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    // Route::get('retrieve/{this.employeeNumber}', [StaffController::class, 'retrieve']);
});
Route::post('register', [StaffController::class, 'register']);
Route::post('generate-code', [StaffController::class, 'generateCode']);
Route::get('retrieve/{employeeNumber?}', [StaffController::class, 'retrieve']);
Route::get('getstaff/{employeeNumber}', [StaffController::class, 'getStaff']);
Route::post('updatestaff/{employeeNumber}', [StaffController::class, 'updateStaff']);
