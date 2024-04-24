<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorld;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;

Route::group([

    'middleware' => 'api'

], function ($router) {
    Route::post('/admin-login', [AuthController::class, 'adminLogin'] );
});

Route::group([

    'middleware' => ['api','auth:api'],

], function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboardInfo'] );
    Route::put('/add-cars', [AdminDashboardController::class, 'addCars']);

});