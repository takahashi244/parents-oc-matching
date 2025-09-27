<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\OCEventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\SchoolController;

// ★ トップは診断フォームに
Route::view('/', 'interest-form');
Route::view('/diagnosis', 'diagnosis')->name('diagnosis.form');

// ★ マッチング & OC詳細（予約は外部）
Route::post('/match', [MatchController::class, 'match'])->name('match.post');
Route::get('/oc/{id}', [OCEventController::class, 'show'])->name('oc.show');
Route::get('/oc/{id}/reserve', [OCEventController::class, 'reserve'])->name('oc.reserve');
Route::get('/oc/{id}/memo/parent', [OCEventController::class, 'showParentMemo'])->name('oc.memo.parent');
Route::post('/oc/{id}/memo/parent', [OCEventController::class, 'storeParentMemo'])->name('oc.memo.parent.store');
Route::get('/oc/{id}/review/confirm', [OCEventController::class, 'confirmParentReview'])->name('oc.review.confirm');
Route::post('/oc/{id}/review/publish', [OCEventController::class, 'publishParentReview'])->name('oc.review.publish');
Route::get('/oc/{id}/memo/child', [OCEventController::class, 'showChildMemo'])->name('oc.memo.child');
Route::post('/oc/{id}/memo/child', [OCEventController::class, 'storeChildMemo'])->name('oc.memo.child.store');
Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
Route::get('/schools/{id}', [SchoolController::class, 'show'])->name('schools.show');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/{rev_id}', [ReviewController::class, 'show'])->name('reviews.show');
Route::post('/share', [ShareController::class, 'store'])->name('share.store');
Route::get('/share/{token}', [ShareController::class, 'show'])->name('share.show');

// （任意）ログイン後ダッシュボードに飛ばさずトップへ
Route::get('/dashboard', fn() => redirect('/'))
    ->middleware(['auth', 'verified'])->name('dashboard');

// Breeze既定のプロフィール周りは残す
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reviews', [ReviewController::class, 'adminIndex'])->name('reviews.index');
    Route::patch('/reviews/{rev_id}', [ReviewController::class, 'moderate'])->name('reviews.moderate');
});
