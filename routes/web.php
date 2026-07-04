<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Admin route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::view('admin/dashboard', 'dashboard-admin')->name('admin.dashboard');

    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::livewire('/create', 'pages::users.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::users.edit')->name('edit');
        Route::livewire('/index', 'pages::users.index')->name('index');
    });

    Route::prefix('admin/categories')->name('categories.')->group(function () {
        Route::livewire('/create', 'pages::categories.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::categories.update')->name('update');
        Route::livewire('/index', 'pages::categories.index')->name('index');
    });

    Route::prefix('admin/genres')->name('genres.')->group(function () {
        Route::livewire('/create', 'pages::genres.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::genres.update')->name('update');
        Route::livewire('/index', 'pages::genres.index')->name('index');
    });

    Route::prefix('admin/books')->name('books.')->group(function () {
        Route::livewire('/create', 'pages::books.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::books.update')->name('update');
        Route::livewire('/index', 'pages::books.index')->name('index');
        Route::livewire('/show/{slug}', 'pages::books.view')->name('show');
    });
});

// User route
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('user.dashboard');
});

require __DIR__ . '/settings.php';
