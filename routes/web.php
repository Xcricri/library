<?php

use App\Http\Controllers\EbookStreamController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::prefix('books')->name('public.')->group(function () {
    Route::livewire('/view/{slug}', 'pages::public-books.view')->name('books.view');
    Route::livewire('/index', 'pages::public-books.index')->name('books.index');
    Route::livewire('/read/{slug}', 'pages::public-books.read')->name('books.read');
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
        Route::livewire('/edit/{id}', 'pages::categories.update')->name('update');
    });

    Route::prefix('admin/genres')->name('genres.')->group(function () {
        Route::livewire('/index', 'pages::genres.index')->name('index');
        Route::livewire('/create', 'pages::genres.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::genres.update')->name('update');
    });

    Route::prefix('admin/books')->name('admin.books.')->group(function () {
        Route::livewire('/index', 'pages::admin-books.index')->name('index');
        Route::livewire('/create', 'pages::admin-books.create')->name('create');
        Route::livewire('/edit/{id}', 'pages::admin-books.update')->name('update');
        Route::livewire('/show/{slug}', 'pages::admin-books.view')->name('show');
        Route::livewire('/read/{slug}', 'pages::admin-books.read')->name('read');

        Route::get('/file/{slug}', [EbookStreamController::class, 'view'])
            ->name('pdf');
    });
});


// User route
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('user.dashboard');

    Route::prefix('user/loans')->name('loans.')->group(function () {
        Route::livewire('/index', 'pages::loans.index')->name('index');
    });

    Route::prefix('user/book')->name('user.books.')->group(function () {
        Route::get('/loancontroller/{id}', [LoanController::class, 'returnBook'])->name('controller');
        Route::livewire('/index', 'pages::user-books.index')->name('index');
    });
});

require __DIR__ . '/settings.php';
