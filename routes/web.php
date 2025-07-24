<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    $role = Auth::user()->role ?? '';
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'teacher') {
        return redirect()->route('teacher.dashboard');
    } elseif ($role === 'parent') {
        return redirect()->route('parent.dashboard');
    }
    return abort(403);
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/teacher/dashboard', [App\Http\Controllers\TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
    Route::get('/parent/dashboard', [App\Http\Controllers\ParentDashboardController::class, 'index'])->name('parent.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('events', App\Http\Controllers\EventController::class);
Route::resource('activities', App\Http\Controllers\ActivityController::class);
Route::resource('learning-materials', App\Http\Controllers\LearningMaterialController::class);
Route::resource('users', App\Http\Controllers\UserController::class);
Route::post('/users/{user}/children', [App\Http\Controllers\UserController::class, 'addChild'])->name('users.add-child');
Route::put('/users/{user}/children/{child}', [App\Http\Controllers\UserController::class, 'updateChild'])->name('users.update-child');
Route::delete('/users/{user}/children/{child}', [App\Http\Controllers\UserController::class, 'deleteChild'])->name('users.delete-child');
Route::resource('child', App\Http\Controllers\ChildController::class);
Route::get('/children/year/{year}', [App\Http\Controllers\ChildController::class, 'childrenByYear'])->name('children.byYear');
Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
Route::patch('/attendance/{child}/status', [App\Http\Controllers\AttendanceController::class, 'updateStatus'])->name('attendance.updateStatus');
Route::patch('/attendance/{child}/comment', [App\Http\Controllers\AttendanceController::class, 'updateComment'])->name('attendance.updateComment');
Route::get('/progress', [App\Http\Controllers\ProfileController::class, 'progress'])->name('progress.index');
Route::post('/progress/save', [App\Http\Controllers\ProfileController::class, 'saveProgress'])->name('progress.save');
Route::get('/attendance/export/pdf', [App\Http\Controllers\AttendanceController::class, 'exportPdf'])->name('attendance.exportPdf');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/learning-materials/approve', [\App\Http\Controllers\LearningMaterialController::class, 'adminApprove'])->name('learning-materials.admin-approve');
    Route::post('/learning-materials/{learningMaterial}/update-status', [\App\Http\Controllers\LearningMaterialController::class, 'adminUpdateStatus'])->name('learning-materials.admin-update-status');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/messages/{user?}', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
});

Route::post('/notifications/mark-read', function() {
    $user = Auth::user();
    if ($user) {
        $user->unreadNotifications->markAsRead();
    }
    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.markRead');

require __DIR__.'/auth.php';
