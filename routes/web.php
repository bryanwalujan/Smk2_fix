<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Teacher\TeacherLmsController;
use App\Http\Controllers\Student\StudentLmsController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

// Public Dashboard Route
Route::get('/', function () {
    return view('public.dashboard');
})->name('public.dashboard');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/teacher/send-task-reminders', [TeacherController::class, 'sendTaskReminders']);

// Admin Routes
Route::middleware(['auth', 'spatie.role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('subjects', SubjectController::class);
    Route::get('qrcode/teacher/{barcode}', [TeacherController::class, 'generateQRCodeImage'])->name('teacher.qrcode');
    Route::resource('classrooms', ClassroomController::class);
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('admin.schedules.index');
    Route::get('classrooms/{classroom}/schedules/create', [ScheduleController::class, 'create'])->name('admin.schedules.create');
    Route::post('classrooms/{classroom}/schedules', [ScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::get('classrooms/{classroom}/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('admin.schedules.edit');
    Route::put('classrooms/{classroom}/schedules/{schedule}', [ScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('classrooms/{classroom}/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
    Route::get('/schedules/{schedule}/sessions', [ScheduleController::class, 'showSessions'])->name('admin.schedules.sessions');
    Route::patch('/schedules/{schedule}/first-session', [ScheduleController::class, 'updateFirstSession'])->name('admin.schedules.update_first_session');
    Route::delete('/schedules/{schedule}/sessions/{session}', [ScheduleController::class, 'deleteSession'])->name('admin.schedules.delete_session');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/attendance/scan', [AttendanceController::class, 'showScanPage'])->name('attendance.scan');
    Route::post('/attendance/scan', [AttendanceController::class, 'scanBarcode'])->name('attendance.scan.post');
    Route::get('/export/students/template', [AdminController::class, 'exportStudentsTemplate'])->middleware('spatie.permission:export_excel')->name('admin.export.students.template');
    Route::post('/import/students', [AdminController::class, 'importStudents'])->middleware('spatie.permission:manage_users')->name('admin.import.students');
    Route::get('/export/teachers/template', [AdminController::class, 'exportTeachersTemplate'])->middleware('spatie.permission:export_excel')->name('admin.export.teachers.template');
    Route::post('/import/teachers', [AdminController::class, 'importTeachers'])->middleware('spatie.permission:manage_users')->name('admin.import.teachers');
    Route::get('/export/classrooms/template', [AdminController::class, 'exportClassroomsTemplate'])->middleware('spatie.permission:export_excel')->name('admin.export.classrooms.template');
    Route::post('/import/classrooms', [AdminController::class, 'importClassrooms'])->middleware('spatie.permission:manage_users')->name('admin.import.classrooms');
    Route::get('/export/subjects/template', [AdminController::class, 'exportSubjectsTemplate'])->middleware('spatie.permission:export_excel')->name('admin.export.subjects.template');
    Route::post('/import/subjects', [AdminController::class, 'importSubjects'])->middleware('spatie.permission:manage_users')->name('admin.import.subjects');
    Route::get('/export/students/excel', [AdminController::class, 'exportStudentsExcel'])->middleware('spatie.permission:export_excel')->name('admin.export.students.excel');
    Route::get('/export/students/pdf', [AdminController::class, 'exportStudentsPdf'])->middleware('spatie.permission:export_pdf')->name('admin.export.students.pdf');
    Route::get('/export/teachers/excel', [AdminController::class, 'exportTeachersExcel'])->middleware('spatie.permission:export_excel')->name('admin.export.teachers.excel');
    Route::get('/export/teachers/pdf', [AdminController::class, 'exportTeachersPdf'])->middleware('spatie.permission:export_pdf')->name('admin.export.teachers.pdf');
    Route::get('/export/classrooms/excel', [AdminController::class, 'exportClassroomsExcel'])->middleware('spatie.permission:export_excel')->name('admin.export.classrooms.excel');
    Route::get('/export/classrooms/pdf', [AdminController::class, 'exportClassroomsPdf'])->middleware('spatie.permission:export_pdf')->name('admin.export.classrooms.pdf');
    Route::get('/export/subjects/excel', [AdminController::class, 'exportSubjectsExcel'])->middleware('spatie.permission:export_excel')->name('admin.export.subjects.excel');
    Route::get('/export/subjects/pdf', [AdminController::class, 'exportSubjectsPdf'])->middleware('spatie.permission:export_pdf')->name('admin.export.subjects.pdf');
    Route::get('/export/attendance/excel', [AdminController::class, 'exportAttendanceExcel'])
        ->middleware('spatie.permission:export_excel')
        ->name('admin.export.attendance.excel');
    Route::get('/export/attendance/pdf', [AdminController::class, 'exportAttendancePdf'])
        ->middleware('spatie.permission:export_pdf')
        ->name('admin.export.attendance.pdf');
    Route::post('/permissions/toggle', [AdminController::class, 'togglePermission'])
        ->name('admin.permissions.toggle')
        ->middleware(['auth', 'spatie.role:admin', 'spatie.permission:manage_roles']);
    Route::get('/permissions', [AdminController::class, 'permissions'])->name('admin.permissions');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/teacher/dashboard', function () {
        return view('teacher.dashboard');
    })->middleware('spatie.role:teacher')->name('teacher.dashboard');

    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->middleware('spatie.role:student')->name('student.dashboard');

    Route::prefix('teacher')->middleware(['auth', 'spatie.role:teacher'])->group(function () {
        Route::get('/lms', [TeacherLmsController::class, 'index'])->name('teacher.lms.index');
        Route::get('/lms/session/{classSession}', [TeacherLmsController::class, 'showSession'])->name('teacher.lms.show_session');
        Route::get('/lms/session/{classSession}/material/create', [TeacherLmsController::class, 'createMaterial'])->name('teacher.lms.create_material');
        Route::post('/lms/session/{classSession}/material', [TeacherLmsController::class, 'storeMaterial'])->name('teacher.lms.store_material');
        Route::get('/lms/session/{classSession}/material/{material}', [TeacherLmsController::class, 'showMaterial'])->name('teacher.lms.show_material');
        Route::get('/lms/session/{classSession}/material/{material}/edit', [TeacherLmsController::class, 'editMaterial'])->name('teacher.lms.edit_material');
        Route::put('/lms/session/{classSession}/material/{material}', [TeacherLmsController::class, 'updateMaterial'])->name('teacher.lms.update_material');
        Route::delete('/lms/session/{classSession}/material/{material}', [TeacherLmsController::class, 'destroyMaterial'])->name('teacher.lms.destroy_material');
        Route::get('/lms/session/{classSession}/assignment/create', [TeacherLmsController::class, 'createAssignment'])->name('teacher.lms.create_assignment');
        Route::post('/lms/session/{classSession}/assignment', [TeacherLmsController::class, 'storeAssignment'])->name('teacher.lms.store_assignment');
        Route::get('/lms/session/{classSession}/assignment/{assignment}', [TeacherLmsController::class, 'showAssignment'])->name('teacher.lms.show_assignment');
        Route::get('/lms/session/{classSession}/assignment/{assignment}/edit', [TeacherLmsController::class, 'editAssignment'])->name('teacher.lms.edit_assignment');
        Route::put('/lms/session/{classSession}/assignment/{assignment}', [TeacherLmsController::class, 'updateAssignment'])->name('teacher.lms.update_assignment');
        Route::delete('/lms/session/{classSession}/assignment/{assignment}', [TeacherLmsController::class, 'destroyAssignment'])->name('teacher.lms.destroy_assignment');
        Route::get('/lms/assignment/{assignment}/submissions', [TeacherLmsController::class, 'showSubmissions'])->name('teacher.lms.show_submissions');
        Route::post('/lms/assignment/{assignment}/submission/{submission}/grade', [TeacherLmsController::class, 'gradeSubmission'])->name('teacher.lms.grade_submission');
        Route::get('/lms/change-password', [TeacherLmsController::class, 'showChangePasswordForm'])->name('teacher.lms.show_change_password');
        Route::post('/lms/change-password', [TeacherLmsController::class, 'changePassword'])->name('teacher.lms.change_password');
        Route::post('/sessions/{classSession}/store-combined', [TeacherLmsController::class, 'storeCombined'])
            ->name('teacher.lms.store_combined');
        Route::get('/lms/sessions/{classSession}/attendance', [TeacherLmsController::class, 'showAttendance'])
            ->name('teacher.lms.show_attendance');
        Route::patch('/lms/sessions/{classSession}/attendance/{student}', [TeacherLmsController::class, 'updateAttendance'])
            ->name('teacher.lms.update_attendance');
        Route::get('/teacher/lms/sessions/{classSession}/submissions/export', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'exportClassSubmissions'])
            ->name('teacher.lms.export_class_submissions')
            ->middleware('auth');
        Route::prefix('teacher/lms')->middleware(['auth', 'role:teacher'])->group(function () {
            Route::get('/', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'index'])->name('teacher.lms.index');
            Route::get('/class/{classroom_id}/schedules', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'showClassSchedules'])->name('teacher.lms.class_schedules');
            Route::post('/teacher/lms/clear-flash', [TeacherLmsController::class, 'clearFlash'])->name('teacher.lms.clear_flash');
            Route::get('/', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'index'])->name('teacher.lms.index');
            Route::get('/class/{classroom_id}/schedules', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'showClassSchedules'])->name('teacher.lms.class_schedules');
            Route::get('/class/{classroom_id}/submissions/export', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'exportClassSubmissions'])->name('teacher.lms.class_submissions_export');
            Route::get('/class/{classroom_id}/attendance/export', [App\Http\Controllers\Teacher\TeacherLmsController::class, 'exportClassAttendance'])->name('teacher.lms.class_attendance_export');

        });
    });

   Route::prefix('student')->middleware(['auth', 'spatie.role:student'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/scan', function () {
            return view('scan.scan');
        })->name('student.scan');
        Route::prefix('lms')->name('lms.')->group(function () {
            Route::get('/', [StudentLmsController::class, 'index'])->name('index');
            Route::get('/sessions/{classSession}', [StudentLmsController::class, 'showSession'])->name('show_session');
            Route::get('/subjects/{subject}/sessions', [StudentLmsController::class, 'subjectSessions'])->name('subject_sessions');
            Route::get('/subjects/{subject}/materials', [StudentLmsController::class, 'subjectMaterials'])->name('subject_materials');
            Route::get('/subjects/{subject}/materials/{material}', [StudentLmsController::class, 'showMaterial'])->name('show_material');
            Route::get('/subjects/{subject}/assignments', [StudentLmsController::class, 'subjectAssignments'])->name('subject_assignments');
            Route::get('/subjects/{subject}/assignments/{assignment}', [StudentLmsController::class, 'showAssignment'])->name('show_assignment');
            Route::get('/assignments/{assignment}/submit', [StudentLmsController::class, 'createSubmission'])->name('create_submission');
            Route::post('/assignments/{assignment}/submit', [StudentLmsController::class, 'storeSubmission'])->name('store_submission');
        });
    });
});
Route::get('/scan', [PublicAttendanceController::class, 'scan'])->name('public.attendance.scan');
Route::post('/scan', [PublicAttendanceController::class, 'processScan'])->name('public.attendance.scan.post');
Route::get('/student/scan', function () {
    return view('scan.scan');
})->name('student.scan')->middleware('auth');