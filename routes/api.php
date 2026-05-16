<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::post('/user', 'userDetails')->middleware('auth:sanctum');
});


// Departments routes
Route::apiResource('departments', DepartmentController::class)
    ->except(['show'])
    ->middleware(['auth:sanctum', 'adminMiddleware']);

Route::get('/departments/{id}', [DepartmentController::class, 'show'])
    ->middleware('auth:sanctum');

// Employees routes
Route::apiResource('employees', EmployeeController::class)
    ->except(['show'])
    ->middleware(['auth:sanctum', 'adminMiddleware']);

Route::get('/employees/{id}', [EmployeeController::class, 'show'])
    ->middleware('auth:sanctum');


// Attendance routes
Route::middleware('auth:sanctum')->controller(AttendanceController::class)->group(function () {
    Route::post('/attendance/check-in', 'checkIn');
    Route::post('/attendance/check-out', 'checkOut');
    Route::get('/attendance/my-history', 'myHistory');
});


Route::get('/attendance', [AttendanceController::class, 'index'])->middleware(['auth:sanctum', 'adminMiddleware']);



// Leaves routes

Route::middleware('auth:sanctum')->controller(LeaveController::class)->group(function () {
    Route::get('/my-leaves', 'myLeaves');
    Route::post('/leaves/apply', 'store');
    Route::delete('/leaves/{id}/cancel', 'cancel');
    Route::get('/leaves/balances', 'getBalances');
});

Route::middleware(['auth:sanctum', 'adminMiddleware'])->controller(LeaveController::class)->group(function () {
    Route::get('/leaves', 'adminIndex');
    Route::patch('/leaves/{id}/decision', 'adminDecision');
});


// Payroll routes


Route::get('/my-payrolls', [PayrollController::class, 'myPayroll'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'adminMiddleware'])->controller(PayrollController::class)->group(function () {
    Route::get('/payrolls', 'index');
    Route::post('/payrolls/generate', 'generate');
    Route::patch('/payrolls/{id}/pay', 'markAsPaid');
});