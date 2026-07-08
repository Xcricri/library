<?php

use App\Http\Controllers\BorrowingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Public route
Route::prefix('books')->name('public.books.')->group(function () {
    Route::livewire('/view/{slug}', 'pages::public-books.view')->name('view');
});

// Admin route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::view('admin/dashboard', 'dashboard-admin')->name('admin.dashboard');

    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::livewire('/index', 'pages::users.index')->name('index');
        Route::livewire('/create', 'pages::users.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::users.edit')->name('edit');
    });

    Route::prefix('admin/categories')->name('categories.')->group(function () {
        Route::livewire('/index', 'pages::categories.index')->name('index');
        Route::livewire('/create', 'pages::categories.create')->name('create');
        Route::livewire('/update/{id}', 'pages::categories.update')->name('update');
    });

    Route::prefix('admin/genres')->name('genres.')->group(function () {
        Route::livewire('/index', 'pages::genres.index')->name('index');
        Route::livewire('/create', 'pages::genres.create')->name('create');
        Route::livewire('/update/{id}', 'pages::genres.update')->name('update');
    });

    Route::prefix('admin/books')->name('admin.books.')->group(function () {
        Route::livewire('/index', 'pages::admin-books.index')->name('index');
        Route::livewire('/create', 'pages::admin-books.create')->name('create');
        Route::livewire('/update/{id}', 'pages::admin-books.update')->name('update');
    });
});

// User route
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('user.dashboard');

    Route::prefix('user/borrowings')->name('borrowings.')->group(function () {
        Route::livewire('/index', 'pages::borrowings.index')->name('index');
    });

    Route::prefix('user/books')->name('user.books.')->group(function () {
        Route::get('/return/{id}', [BorrowingController::class, 'returnBook'])->name('return');
        Route::livewire('/index', 'pages::user-books.index')->name('index');
    });

    Route::prefix('user/wishlist')->name('wishlists.')->group(function () {
        Route::livewire('/index', 'pages::wishlists.index')->name('index');
    });
});

require __DIR__ . '/settings.php';
