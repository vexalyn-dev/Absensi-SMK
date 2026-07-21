<?php

use App\Http\Controllers\DashboardController as AdminDashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AttendanceHistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\Teacher\ClassAttendanceController as TeacherClassAttendanceController;
use App\Http\Controllers\Teacher\WorkScheduleController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TeacherScheduleController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\TeachingScheduleController;
use App\Http\Controllers\ClassAttendanceController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\HistoryController as TeacherHistoryController;
use App\Http\Controllers\Teacher\LeaveController as TeacherLeaveController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\NotificationController as TeacherNotificationController;
use App\Http\Controllers\Admin\LeaveApprovalController;
use App\Http\Controllers\Admin\ManualClassAttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect welcome page to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return redirect()->route('login');
    })->name('register');

    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

// Social Login Routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/leaves/{leave}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::get('/teacher/leave/{leaveRequest}', [TeacherLeaveController::class, 'show'])->name('teacher.leave.show');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Attendance
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/check-status/{teacherId}', [AttendanceController::class, 'checkStatus'])->name('attendance.check-status');
    Route::get('/attendance/history', [AttendanceHistoryController::class, 'index'])->name('attendance.history');
    Route::get('/attendance/export', [AttendanceHistoryController::class, 'export'])->name('attendance.export');
    Route::get('/attendance/scan', [QrCodeController::class, 'scan'])->name('attendance.scan');
    Route::post('/attendance/scan', [QrCodeController::class, 'processScan'])->name('attendance.scan.process');
    
    // Teachers
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    
    // --- Route Import, Export & Bulk Actions ---

    Route::post('/teachers/bulk-toggle', [TeacherController::class, 'bulkToggleStatus'])->name('teachers.bulk-toggle');
    Route::delete('/teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])->name('teachers.bulk-delete'); 
    // -------------------------------------------

    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::post('/teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
    Route::get('/teachers/{teacher}/qr', [QrCodeController::class, 'show'])->name('teachers.qr');
    Route::get('/teachers/{teacher}/qr/download', [QrCodeController::class, 'download'])->name('teachers.qr.download');
    Route::post('/teachers/{teacher}/qr/regenerate', [QrCodeController::class, 'regenerate'])->name('teachers.qr.regenerate');
    Route::get('/teachers/{teacher}/data', [TeacherController::class, 'getData'])->name('teachers.data');
    
    // Subjects
    Route::resource('subjects', SubjectController::class);
    
    // Classes
    // Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    // Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    // Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    // Route::get('/classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
    // Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    // Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    
    Route::get('/schedules', [TeacherScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/{teacher}/edit', [TeacherScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{teacher}', [TeacherScheduleController::class, 'update'])->name('schedules.update');

    // Data Kelas
    Route::resource('classrooms', ClassroomController::class);
    Route::get('/classrooms/{classroom}/qr', [ClassroomController::class, 'qrCode'])->name('classrooms.qr');
    
    // Jadwal Mengajar
    Route::get('/teaching-schedules', [TeachingScheduleController::class, 'index'])->name('teaching-schedules.index');
    Route::get('/teaching-schedules/{teacher}/edit', [TeachingScheduleController::class, 'edit'])->name('teaching-schedules.edit');
    Route::put('/teaching-schedules/{teacher}', [TeachingScheduleController::class, 'update'])->name('teaching-schedules.update');
    
    // Presensi Per Kelas
    Route::get('/class-attendance/scan', [ClassAttendanceController::class, 'scan'])->name('class-attendance.scan');
    Route::post('/class-attendance', [ClassAttendanceController::class, 'store'])->name('class-attendance.store');
    Route::get('/class-attendance/history', [ClassAttendanceController::class, 'history'])->name('class-attendance.history');
    
    // Leaves
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::post('/leaves/{leaveRequest}/approve', [LeaveApprovalController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leaveRequest}/reject', [LeaveApprovalController::class, 'reject'])->name('leaves.reject');
    Route::get('/my-leaves', [LeaveController::class, 'myLeaves'])->name('leaves.my');
    
    // Reports
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/settings/attendance', [SettingsController::class, 'updateAttendance'])->name('settings.attendance');
    Route::post('/settings/appearance', [SettingsController::class, 'updateAppearance'])->name('settings.appearance');
    Route::post('/settings/notification', [SettingsController::class, 'updateNotification'])->name('settings.notification');
    Route::post('/settings/reset', [SettingsController::class, 'resetSettings'])->name('settings.reset');
    
    // Messages / CS Chat
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'fetchMessages'])->name('messages.fetch');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/admin/notifications', fn() => redirect()->route('notifications.index'))->name('admin.notifications'); // Admin alias
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.read-all');
    Route::delete('/notifications/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');

    // Holiday Management
    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/holidays/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');
    Route::post('/holidays/fetch-national', [HolidayController::class, 'fetchNationalHolidays'])->name('holidays.fetch-national');

    // Manual Class Attendance - dipindah ke grup admin dengan prefix
});

// Admin Manual Class Attendance (dengan prefix admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/class-attendance/manual', [ManualClassAttendanceController::class, 'index'])->name('class-attendance.manual');
    Route::post('/class-attendance/manual', [ManualClassAttendanceController::class, 'store'])->name('class-attendance.manual.store');
    Route::delete('/class-attendance/manual/{id}', [ManualClassAttendanceController::class, 'destroy'])->name('class-attendance.manual.destroy');
});

// Teacher Routes
Route::middleware(['auth', 'role:guru'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/schedule', [\App\Http\Controllers\Teacher\ScheduleController::class, 'index'])->name('schedule');
        Route::get('/work-schedule', [WorkScheduleController::class, 'index'])->name('work-schedule');
        Route::get('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance');
        Route::post('/attendance/store', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
        
        // Class Attendance
        Route::get('/class-attendance', [TeacherClassAttendanceController::class, 'index'])->name('class-attendance');
        Route::post('/class-attendance/scan', [TeacherClassAttendanceController::class, 'scan'])->name('class-attendance.scan');
        Route::post('/class-attendance/save-shared', [TeacherClassAttendanceController::class, 'saveSharedSpaceAttendance'])->name('class-attendance.save-shared');
        
        Route::get('/profile', [TeacherProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [TeacherProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('/history', [TeacherHistoryController::class, 'index'])->name('history');
        Route::get('/history/data', [TeacherHistoryController::class, 'getData'])->name('history.data');
        Route::get('/history-data', [TeacherHistoryController::class, 'getData']);
        Route::get('/history/export', [TeacherHistoryController::class, 'export'])->name('history.export');

        Route::get('/notifications', [TeacherNotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{id}/read', [TeacherNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [TeacherNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notifications/{id}', [TeacherNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('/notifications/bulk-delete', [TeacherNotificationController::class, 'bulkDelete'])->name('notifications.bulk-delete');

        Route::get('/leave', [TeacherLeaveController::class, 'index'])->name('leave');
        Route::get('/leave/create', [TeacherLeaveController::class, 'create'])->name('leave.create');
        Route::post('/leave', [TeacherLeaveController::class, 'store'])->name('leave.store');
        Route::delete('/leave/{leaveRequest}', [TeacherLeaveController::class, 'destroy'])->name('leave.destroy');

        Route::get('/leaves/create', [TeacherLeaveController::class, 'create'])->name('leaves.create');
        Route::post('/leave-request', [TeacherLeaveController::class, 'store'])->name('leave-request.store');
        Route::post('/today-notes', [TeacherDashboardController::class, 'updateTodayNotes'])->name('today-notes.update');
        Route::get('/leaves', [TeacherLeaveController::class, 'index'])->name('leaves');
});
