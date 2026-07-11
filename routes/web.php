<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\AttemptController as AdminAttemptController;
use App\Http\Controllers\Participant\QuizController as ParticipantQuizController;
use Illuminate\Support\Facades\Route;

// Redirect welcome page to appropriate dashboard or login
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('participant.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Administrator Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminQuizController::class, 'index'])->name('admin.dashboard');
    
    // Quiz CRUD
    Route::resource('quizzes', AdminQuizController::class)->except(['index'])->names([
        'create'  => 'admin.quizzes.create',
        'store'   => 'admin.quizzes.store',
        'show'    => 'admin.quizzes.show',
        'edit'    => 'admin.quizzes.edit',
        'update'  => 'admin.quizzes.update',
        'destroy' => 'admin.quizzes.destroy',
    ]);
    Route::patch('/quizzes/{quiz}/toggle-publish', [AdminQuizController::class, 'togglePublish'])->name('admin.quizzes.toggle-publish');

    // Question Management (scoped under Quiz)
    Route::get('/quizzes/{quiz}/questions/create', [AdminQuestionController::class, 'create'])->name('admin.questions.create');
    Route::post('/quizzes/{quiz}/questions', [AdminQuestionController::class, 'store'])->name('admin.questions.store');
    Route::get('/quizzes/{quiz}/questions/{question}/edit', [AdminQuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::put('/quizzes/{quiz}/questions/{question}', [AdminQuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('/quizzes/{quiz}/questions/{question}', [AdminQuestionController::class, 'destroy'])->name('admin.questions.destroy');

    // Attempt Review & Grading
    Route::get('/attempts/{attempt}', [AdminAttemptController::class, 'show'])->name('admin.attempts.show');
    Route::post('/attempts/{attempt}/grade', [AdminAttemptController::class, 'grade'])->name('admin.attempts.grade');
});

// Participant Routes
Route::middleware(['auth', 'participant'])->prefix('participant')->group(function () {
    Route::get('/dashboard', [ParticipantQuizController::class, 'dashboard'])->name('participant.dashboard');
    
    // Quiz Engine
    Route::post('/quizzes/{quiz}/start', [ParticipantQuizController::class, 'start'])->name('participant.quizzes.start');
    Route::get('/attempts/{attempt}/take', [ParticipantQuizController::class, 'take'])->name('participant.attempts.take');
    Route::post('/attempts/{attempt}/submit', [ParticipantQuizController::class, 'submit'])->name('participant.attempts.submit');
    Route::get('/attempts/{attempt}', [ParticipantQuizController::class, 'showAttempt'])->name('participant.attempts.show');
});
