<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Admin route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::view('admin/dashboard', 'dashboard-admin')->name('dashboard');
});

// User route
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});


require __DIR__ . '/settings.php';
