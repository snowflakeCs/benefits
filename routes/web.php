<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Swagger documentation route
Route::get('/api/documentation', function () {
    return view('swagger');
});