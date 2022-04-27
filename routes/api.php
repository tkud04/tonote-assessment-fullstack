<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', [APIController::class,'getIndex']);
Route::get('create-admin', [APIController::class,'getCreateAdmin']);
Route::post('create-admin', [APIController::class,'postCreateAdmin']);
Route::get('admin-login', [APIController::class,'getAdminLogin']);
Route::post('admin-login', [APIController::class,'postAdminLogin']);
Route::get('new-employee', [APIController::class,'getCreateEmployee']);
Route::post('new-employee', [APIController::class,'postCreateEmployee']);
Route::get('delete-employee', [APIController::class,'getDeleteEmployee']);
Route::get('update-employee', [APIController::class,'getUpdateEmployee']);
Route::post('update-employee', [APIController::class,'postUpdateEmployee']);
Route::get('employees', [APIController::class,'getEmployees']);
Route::get('employee', [APIController::class,'getEmployee']);
Route::get('assign-leave', [APIController::class,'getAssignLeave']);
