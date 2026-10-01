<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;

// --- Rute Pelanggan (Tanpa Login) ---
// Halaman utama / katalog menu
Route::get('/', [OrderController::class, 'index'])->name('customer.index');

// Proses pemesanan makanan
Route::post('/checkout', [OrderController::class, 'store'])->name('customer.checkout');

// --- Dashboard Admin ---
Route::get('/dashboard', [OrderController::class, 'adminDashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// --- Halaman Admin (Wajib Login) ---
Route::middleware('auth')->group(function () {
    // Pengaturan profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard pesanan admin
    Route::get('/admin/dashboard', [OrderController::class, 'adminDashboard'])->name('admin.dashboard');

    // Update status pesanan
    Route::patch('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    
    // Kelola menu makanan (CRUD)
    Route::resource('/admin/foods', FoodController::class);
});

// Rute auth (login, register, logout)
require __DIR__.'/auth.php';
