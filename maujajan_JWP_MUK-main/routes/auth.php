<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// RUTE AUTENTIKASI (LARAVEL BREEZE)
// =========================================================================
// File ini mendefinisikan rute login, registrasi, lupa password, dan logout.

// 1. RUTE UNTUK PENGUNJUNG / TAMU (BELUM LOGIN / GUEST)
Route::middleware('guest')->group(function () {
    // Registrasi User Baru
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register'); // Menampilkan form register
    Route::post('register', [RegisteredUserController::class, 'store']); // Memproses simpan user baru

    // Login Pengguna / Admin
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login'); // Menampilkan halaman form login
    Route::post('login', [AuthenticatedSessionController::class, 'store']); // Memproses validasi dan pembuatan sesi login

    // Lupa Password (Request Reset Link via Email)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Reset Password Baru (Dengan Token)
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// 2. RUTE UNTUK PENGGUNA TEROTENTIKASI (SUDAH LOGIN / AUTH)
Route::middleware('auth')->group(function () {
    // Verifikasi Alamat Email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Konfirmasi Password Sebelum Aksi Sensitif
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Update Password Pengguna
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout: Menghapus sesi login dan kembali ke halaman publik
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
