<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\KRSController;
use App\Http\Controllers\Client\ProjectController;
use App\Http\Controllers\Payment\EscrowController;
use App\Http\Controllers\Workroom\WorkroomController;
use App\Http\Controllers\Portfolio\PortfolioController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\AdminController;

// ============================================================
// Guest Routes (Auth)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root to login
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'mahasiswa' => redirect()->route('student.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// ============================================================
// Student Routes
// ============================================================
Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
    Route::get('/projects', [StudentController::class, 'projects'])->name('projects');

    // KRS
    Route::get('/krs', [KRSController::class, 'show'])->name('krs');
    Route::post('/krs/upload', [KRSController::class, 'upload'])->name('krs.upload');
    Route::post('/krs/reparse', [KRSController::class, 'reparse'])->name('krs.reparse');

    // Portfolio
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
    Route::post('/portfolio/{portfolio}/toggle', [PortfolioController::class, 'toggleVisibility'])->name('portfolio.toggle');
});

// ============================================================
// Client Routes
// ============================================================
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ProjectController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('create-project');
    Route::post('/projects', [ProjectController::class, 'store'])->name('store-project');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('project-detail');
    Route::get('/projects/{project}/candidates', [ProjectController::class, 'candidates'])->name('candidates');
    Route::post('/projects/{project}/select', [ProjectController::class, 'selectCandidate'])->name('select-candidate');
});

// ============================================================
// Payment / Escrow Routes
// ============================================================
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/escrow/{project}', [EscrowController::class, 'show'])->name('escrow');
    Route::post('/escrow/{project}/deposit', [EscrowController::class, 'deposit'])->name('deposit');
    Route::post('/payment/{payment}/callback', [EscrowController::class, 'callback'])->name('callback');
    Route::post('/payment/{payment}/release', [EscrowController::class, 'release'])->name('release');
    Route::post('/payment/{payment}/refund', [EscrowController::class, 'refund'])->name('refund');
});

// ============================================================
// Workroom Routes
// ============================================================
Route::middleware(['auth'])->prefix('workroom')->name('workroom.')->group(function () {
    Route::get('/{project}', [WorkroomController::class, 'show'])->name('show');
    Route::post('/{workroom}/message', [WorkroomController::class, 'sendMessage'])->name('message');
    Route::post('/{workroom}/upload', [WorkroomController::class, 'uploadDeliverable'])->name('upload');
    Route::post('/{workroom}/progress', [WorkroomController::class, 'updateProgress'])->name('progress');
    Route::post('/{workroom}/revision', [WorkroomController::class, 'requestRevision'])->name('revision');
    Route::post('/{workroom}/approve', [WorkroomController::class, 'approveWork'])->name('approve');
    Route::post('/{workroom}/dispute', [WorkroomController::class, 'disputeProject'])->name('dispute');
});

// ============================================================
// Analytics Routes
// ============================================================
Route::middleware(['auth'])->prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
});

// ============================================================
// Admin Routes
// ============================================================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/dispute/{project}/{action}', [AdminController::class, 'resolveDispute'])->name('resolve-dispute');
});

// Public Portfolio
Route::get('/portfolio/{student}', [PortfolioController::class, 'show'])->name('portfolio.public');
