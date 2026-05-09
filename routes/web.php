<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CollegeController as AdminCollegeController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\FirmController as AdminFirmController;
use App\Http\Controllers\Admin\MentorController as AdminMentorController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;

use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\College\ProfileController as CollegeProfileController;
use App\Http\Controllers\College\CourseController as CollegeCourseController;
use App\Http\Controllers\College\MentorController as CollegeMentorController;
use App\Http\Controllers\College\EnrollmentController as CollegeEnrollmentController;


use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;

use App\Http\Controllers\Firm\DashboardController as FirmDashboardController;
use App\Http\Controllers\Firm\CourseController as FirmCourseController;

use App\Http\Controllers\ProfileController;
use App\Models\College;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/students', AdminStudentController::class)->names('students');
        Route::resource('/colleges', AdminCollegeController::class)->names('colleges');
        Route::resource('/mentors', AdminMentorController::class)->names('mentors');
        Route::resource('/firms', AdminFirmController::class)->names('firms');
        Route::resource('/categories', AdminCategoryController::class)->names('categories');
        Route::resource('/courses', AdminCourseController::class)->names('courses');
        Route::resource('/enrollments', AdminEnrollmentController::class)->names('enrollments');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

Route::middleware(['auth','role:college'])->group(function(){
    Route::prefix('college')->name('college.')->group(function(){
        Route::get('/complete-profile', [CollegeProfileController::class, 'completeEdit'])->name('complete.profile.edit');
        Route::patch('/complete-profile', [CollegeProfileController::class, 'completeUpdate'])->name('complete.profile.update');
        Route::get('/profile', [CollegeProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [CollegeProfileController::class, 'update'])->name('profile.update');
        Route::get('/dashboard', [CollegeDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/courses', CollegeCourseController::class)->names('courses');
        Route::resource('/mentors', CollegeMentorController::class)->names('mentors');
        Route::resource('/enrollments', CollegeEnrollmentController::class)->names('enrollments');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware(['auth', 'role:student'])->group(function () {
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/explore', [StudentCourseController::class, 'index'])->name('explore.index');
        Route::get('/courses/{slug}', [StudentCourseController::class, 'show'])->name('course.show');

        // Student enrollment (creates an enrollment request)
        Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])
            ->name('course.enroll');

        // List student's enrollments
        Route::get('/my-enrollments', [StudentCourseController::class, 'myEnrollments'])
            ->name('my.enrollments');
        // Payment view and processing for a student's enrollment
        Route::get('/enrollments/{enrollment}/payment', [StudentCourseController::class, 'payment'])
            ->name('enrollment.payment');
        Route::post('/enrollments/{enrollment}/pay', [StudentCourseController::class, 'processPayment'])
            ->name('enrollment.pay');
        Route::delete('/my-enrollments/{enrollment}', [StudentCourseController::class, 'destroy'])
            ->name('my.enrollments.destroy');

        Route::get('/complete-profile', [StudentProfileController::class, 'CompleteEdit'])->name('complete.profile.edit');
        Route::patch('/complete-profile', [StudentProfileController::class, 'CompleteUpdate'])->name('complete.profile.update');
        Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [StudentProfileController::class, 'update'])->name('profile.update');

    });
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:firm'])->group(function () {
    Route::prefix('firm')->name('firm.')->group(function () {
        Route::get('/dashboard', [FirmDashboardController::class, 'index'])->name('dashboard');
        Route::get('/explore', [FirmCourseController::class, 'index'])->name('explore.index');
        Route::get('/courses/{slug}', [FirmCourseController::class, 'show'])->name('course.show');
        Route::post('/courses/{course}/book', [FirmCourseController::class, 'book'])
            ->name('course.book');
        Route::get('/bookings', [FirmCourseController::class, 'bookings'])->name('bookings');
        // // Payment view and processing for a student's enrollment
        // Route::get('/enrollments/{enrollment}/payment', [StudentCourseController::class, 'payment'])
        //     ->name('enrollment.payment');
        // Route::post('/enrollments/{enrollment}/pay', [StudentCourseController::class, 'processPayment'])
        //     ->name('enrollment.pay');
        // Route::delete('/my-enrollments/{enrollment}', [StudentCourseController::class, 'destroy'])
        //     ->name('my.enrollments.destroy');

        // Route::get('/complete-profile', [StudentProfileController::class, 'CompleteEdit'])->name('complete.profile.edit');
        // Route::patch('/complete-profile', [StudentProfileController::class, 'CompleteUpdate'])->name('complete.profile.update');
        // Route::get('/profile', [StudentProfileController::class, 'edit'])->name('profile.edit');
        // Route::patch('/profile', [StudentProfileController::class, 'update'])->name('profile.update');

    });
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




require __DIR__.'/auth.php';
