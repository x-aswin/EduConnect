<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/students', AdminStudentController::class)->names('students');
    });
});


Route::middleware(['auth', 'role:student'])->group(function () {
    Route::prefix('student')->name('student.')->group(function () {
        // The page to show the form
        Route::get('/complete-profile', [StudentProfileController::class, 'edit'])->name('complete.profile.edit');
        // The logic to handle the form submission
        Route::patch('/complete-profile', [StudentProfileController::class, 'update'])->name('complete.profile.update');

    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
