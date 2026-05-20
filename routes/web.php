<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\MenuController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\Api;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ── Landing ──────────────────────────────────────────────
Route::get('/', fn() => view('landing'))->name('home');

// ── Auth ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,    'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class,    'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Onboarding ──────────────────────────────────────────
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/step/{step}',  [OnboardingController::class, 'showStep'])->name('step');
    Route::post('/step/1',      [OnboardingController::class, 'saveStep1'])->name('save.1');
    Route::post('/step/2',      [OnboardingController::class, 'saveStep2'])->name('save.2');
    Route::post('/step/3',      [OnboardingController::class, 'saveStep3'])->name('save.3');
    Route::post('/step/4',      [OnboardingController::class, 'saveStep4'])->name('save.4');
    Route::post('/step/5',      [OnboardingController::class, 'saveStep5'])->name('save.5');
});

// ── User Area ───────────────────────────────────────────
Route::middleware(['auth','user.only','onboarding'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/',             [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu',         [MenuController::class,      'index'])->name('menu');
    Route::post('/menu/regenerate', [MenuController::class,  'regenerate'])->name('menu.regenerate');
    Route::post('/menu/log',    [MenuController::class,      'logFood'])->name('menu.log');
    Route::get('/history',      [HistoryController::class,   'index'])->name('history');
    Route::delete('/history/{history}', [HistoryController::class, 'destroy'])->name('history.destroy');
    Route::get('/profile',      [ProfileController::class,   'index'])->name('profile');
    Route::post('/profile/health',     [ProfileController::class, 'updateHealth'])->name('profile.health');
    Route::post('/profile/allergies',  [ProfileController::class, 'updateAllergies'])->name('profile.allergies');
    Route::post('/profile/reminders',  [ProfileController::class, 'updateReminders'])->name('profile.reminders');
    Route::post('/profile/password',   [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::get('/notifications',       [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read', [NotificationController::class, 'markAllRead'])->name('notifications.read');
});

// ── API Routes (JSON) ────────────────────────────────────
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    Route::get('user/calorie-summary',      [Api\MenuApiController::class, 'calorieSummary'])->name('calorie.summary');
    Route::post('menu/regenerate',          [Api\MenuApiController::class, 'regenerateMenu'])->name('menu.regenerate');
    Route::post('menu/log',                 [Api\MenuApiController::class, 'logFood'])->name('menu.log');
    Route::get('foods/search',              [Api\MenuApiController::class, 'searchFoods'])->name('foods.search');
    Route::get('notifications/unread-count',[Api\MenuApiController::class, 'unreadCount'])->name('notif.count');
});

// ── Admin Area ──────────────────────────────────────────
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',         [Admin\DashboardController::class,       'index'])->name('dashboard');
    Route::resource('users',Admin\UserManagementController::class)->only(['index','show','destroy']);
    Route::resource('foods',Admin\FoodController::class);
    Route::resource('articles', Admin\ArticleController::class);
});