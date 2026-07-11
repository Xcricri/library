<?php

use App\Http\Controllers\BorrowingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Public route
Route::prefix('books')->name('public.books.')->group(function () {
    Route::livewire('/view/{slug}', 'pages::public-books.view')->name('view');
});

// Admin route
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('admin/dashboard', 'dashboard-admin')->name('admin.dashboard');

    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::livewire('/index', 'pages::users.index')->name('index');
        Route::livewire('/create', 'pages::users.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::users.edit')->name('edit');
    });
});

// Member route
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('member.dashboard');

    Route::prefix('member/borrowings')->name('borrowings.')->group(function () {
        Route::livewire('/index', 'pages::borrowings.index')->name('index');
    });

    Route::prefix('member/books')->name('member.books.')->group(function () {
        Route::post('/return/{id}', [BorrowingController::class, 'returnBook'])->name('return');
        Route::livewire('/index', 'pages::member-books.index')->name('index');
    });

    Route::prefix('member/wishlist')->name('wishlists.')->group(function () {
        Route::livewire('/index', 'pages::wishlists.index')->name('index');
    });
});

// Staff route
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::view('staff/dashboard', 'dashboard-staff')->name('staff.dashboard');

    Route::prefix('staff/categories')->name('categories.')->group(function () {
        Route::livewire('/index', 'pages::categories.index')->name('index');
        Route::livewire('/create', 'pages::categories.create')->name('create');
        Route::livewire('/update/{id}', 'pages::categories.update')->name('update');
    });

    Route::prefix('staff/genres')->name('genres.')->group(function () {
        Route::livewire('/index', 'pages::genres.index')->name('index');
        Route::livewire('/create', 'pages::genres.create')->name('create');
        Route::livewire('/update/{id}', 'pages::genres.update')->name('update');
    });

    Route::prefix('staff/books')->name('staff.books.')->group(function () {
        Route::livewire('/index', 'pages::staff-books.index')->name('index');
        Route::livewire('/create', 'pages::staff-books.create')->name('create');
        Route::livewire('/update/{id}', 'pages::staff-books.update')->name('update');
    });
});

require __DIR__ . '/settings.php';
