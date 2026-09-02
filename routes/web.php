<?php

use App\Http\Controllers\Admin\AcademicController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProgrammeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RubricController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Hod\ApprovalController as HodApprovalController;
use App\Http\Controllers\Hod\DashboardController as HodDashboardController;
use App\Http\Controllers\Instructor\AssessmentController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\RosterController;
use App\Http\Controllers\Instructor\SubmissionController as InstructorSubmissionController;
use App\Http\Controllers\Student\PortfolioController;
use App\Http\Controllers\Student\SubmissionController as StudentSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    $user = auth()->user();
    if ($user->isHod()) {
        return redirect()->route('hod.dashboard');
    }
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'instructor' => redirect()->route('instructor.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});
Route::middleware('auth')->post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Public shared portfolio (no auth) - stretch goal
Route::get('/p/{token}', [PortfolioController::class, 'publicShow'])->name('portfolio.public');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    Route::get('/programmes', [ProgrammeController::class, 'index'])->name('programmes.index');
    Route::post('/programmes', [ProgrammeController::class, 'store'])->name('programmes.store');
    Route::put('/programmes/{programme}', [ProgrammeController::class, 'update'])->name('programmes.update');
    Route::delete('/programmes/{programme}', [ProgrammeController::class, 'destroy'])->name('programmes.destroy');

    Route::get('/academic', [AcademicController::class, 'index'])->name('academic.index');
    Route::post('/academic/years', [AcademicController::class, 'storeYear'])->name('academic.years.store');
    Route::patch('/academic/years/{academicYear}/current', [AcademicController::class, 'setCurrentYear'])->name('academic.years.setCurrent');
    Route::delete('/academic/years/{academicYear}', [AcademicController::class, 'destroyYear'])->name('academic.years.destroy');
    Route::post('/academic/years/{academicYear}/semesters', [AcademicController::class, 'storeSemester'])->name('academic.semesters.store');
    Route::patch('/academic/semesters/{semester}/current', [AcademicController::class, 'setCurrentSemester'])->name('academic.semesters.setCurrent');
    Route::delete('/academic/semesters/{semester}', [AcademicController::class, 'destroySemester'])->name('academic.semesters.destroy');

    Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
    Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
    Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
    Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('/rubrics', [RubricController::class, 'index'])->name('rubrics.index');
    Route::post('/rubrics', [RubricController::class, 'store'])->name('rubrics.store');
    Route::put('/rubrics/{rubric}', [RubricController::class, 'update'])->name('rubrics.update');
    Route::delete('/rubrics/{rubric}', [RubricController::class, 'destroy'])->name('rubrics.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/students/{user}/portfolio', [PortfolioController::class, 'showFor'])->name('students.portfolio');
});

// HOD (role=instructor + is_hod=true)
Route::middleware(['auth', 'role:hod'])->prefix('hod')->name('hod.')->group(function () {
    Route::get('/dashboard', [HodDashboardController::class, 'index'])->name('dashboard');
    Route::get('/approvals', [HodApprovalController::class, 'index'])->name('approvals.index');
    Route::patch('/approvals/{assessment}/approve', [HodApprovalController::class, 'approve'])->name('approvals.approve');
    Route::get('/students/{user}/portfolio', [PortfolioController::class, 'showFor'])->name('students.portfolio');
});

// Instructor
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::get('/assessments/courses/{course}', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/assessments/courses/{course}', [AssessmentController::class, 'store'])->name('assessments.store');

    Route::get('/roster/courses/{course}', [RosterController::class, 'edit'])->name('roster.edit');
    Route::put('/roster/courses/{course}', [RosterController::class, 'update'])->name('roster.update');

    Route::get('/students/{user}/portfolio', [PortfolioController::class, 'showFor'])->name('students.portfolio');

    Route::get('/submissions', [InstructorSubmissionController::class, 'index'])->name('submissions.index');
    Route::patch('/submissions/{submission}/review', [InstructorSubmissionController::class, 'markReviewed'])->name('submissions.review');
});

// Student
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [PortfolioController::class, 'show'])->name('dashboard');
    Route::get('/portfolio', [PortfolioController::class, 'show'])->name('portfolio');
    Route::get('/portfolio/export', [PortfolioController::class, 'exportPdf'])->name('portfolio.export');
    Route::post('/portfolio/share', [PortfolioController::class, 'share'])->name('portfolio.share');
    Route::delete('/portfolio/share', [PortfolioController::class, 'revokeShare'])->name('portfolio.share.revoke');

    Route::get('/submissions', [StudentSubmissionController::class, 'index'])->name('submissions.index');
    Route::post('/submissions', [StudentSubmissionController::class, 'store'])->name('submissions.store');
});
