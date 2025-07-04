<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Teacher\TeacherLmsController;
use App\Http\Controllers\Student\StudentLmsController;

// Public Dashboard Route
Route::get('/', function () {
    return view('public.dashboard');
})->name('public.dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::get('qrcode/teacher/{barcode}', [TeacherController::class, 'generateQRCodeImage'])->name('teacher.qrcode');
    Route::resource('classrooms', ClassroomController::class);
    Route::get('classrooms/{classroom}/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('classrooms/{classroom}/schedules', [ScheduleController::class, 'store'])->name('schedules.store');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/scan', [AttendanceController::class, 'showScanPage'])->name('attendance.scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scanBarcode'])->name('attendance.scan.post');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->middleware('role:student')->name('student.dashboard');

    Route::prefix('teacher/lms')->name('teacher.lms.')->middleware('role:teacher')->group(function () {
        Route::get('/', [TeacherLmsController::class, 'index'])->name('index');
        Route::get('/sessions/create', [TeacherLmsController::class, 'createSession'])->name('create_session');
        Route::post('/sessions', [TeacherLmsController::class, 'storeSession'])->name('store_session');
        Route::get('/sessions/{classSession}/edit', [TeacherLmsController::class, 'editSession'])->name('edit_session');
        Route::put('/sessions/{classSession}', [TeacherLmsController::class, 'updateSession'])->name('update_session');
        Route::get('/sessions/{classSession}', [TeacherLmsController::class, 'showSession'])->name('show_session');
        Route::delete('/sessions/{classSession}', [TeacherLmsController::class, 'destroySession'])->name('destroy_session');
        Route::get('/sessions/{classSession}/materials/create', [TeacherLmsController::class, 'createMaterial'])->name('create_material');
        Route::post('/sessions/{classSession}/materials', [TeacherLmsController::class, 'storeMaterial'])->name('store_material');
        Route::get('/sessions/{classSession}/materials/{material}', [TeacherLmsController::class, 'showMaterial'])->name('show_material');
        Route::get('/sessions/{classSession}/materials/{material}/edit', [TeacherLmsController::class, 'editMaterial'])->name('edit_material');
        Route::put('/sessions/{classSession}/materials/{material}', [TeacherLmsController::class, 'updateMaterial'])->name('update_material');
        Route::delete('/sessions/{classSession}/materials/{material}', [TeacherLmsController::class, 'destroyMaterial'])->name('destroy_material');
        Route::get('/sessions/{classSession}/assignments/create', [TeacherLmsController::class, 'createAssignment'])->name('create_assignment');
        Route::post('/sessions/{classSession}/assignments', [TeacherLmsController::class, 'storeAssignment'])->name('store_assignment');
        Route::get('/sessions/{classSession}/assignments/{assignment}', [TeacherLmsController::class, 'showAssignment'])->name('show_assignment');
        Route::get('/sessions/{classSession}/assignments/{assignment}/edit', [TeacherLmsController::class, 'editAssignment'])->name('edit_assignment');
       Route::put('/sessions/{classSession}/assignments/{assignment}', [TeacherLmsController::class, 'updateAssignment'])->name('update_assignment');
        Route::delete('/assignments/{assignment}', [TeacherLmsController::class, 'destroyAssignment'])->name('destroy_assignment');
        Route::get('/assignments/{assignment}/submissions', [TeacherLmsController::class, 'showSubmissions'])->name('show_submissions');
        Route::get('/change-password', [TeacherLmsController::class, 'showChangePasswordForm'])->name('change_password');
        Route::post('/change-password', [TeacherLmsController::class, 'changePassword'])->name('change_password.store');
    });

    Route::prefix('student/lms')->name('student.lms.')->middleware('role:student')->group(function () {
        Route::get('/', [StudentLmsController::class, 'index'])->name('index');
        Route::get('/sessions/{classSession}', [StudentLmsController::class, 'showSession'])->name('show_session');
        Route::get('/assignments/{assignment}/submit', [StudentLmsController::class, 'createSubmission'])->name('create_submission');
        Route::post('/assignments/{assignment}/submit', [StudentLmsController::class, 'storeSubmission'])->name('store_submission');
    });
});