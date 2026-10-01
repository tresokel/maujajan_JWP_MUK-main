<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// =========================================================================
// AUTHENTICATED SESSION CONTROLLER (MANAJEMEN LOGIN & LOGOUT PENGGUNA)
// =========================================================================
// Controller ini menangani proses otentikasi login admin/user, regenerasi sesi,
// dan pemutusan sesi (logout) dengan standar keamanan Laravel Breeze.
class AuthenticatedSessionController extends Controller
{
    /**
     * [LOGIN - TAMPILAN] Menampilkan halaman formulir login.
     * URL: GET /login
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        // Mengembalikan view formulir login (resources/views/auth/login.blade.php)
        return view('auth.login');
    }

    /**
     * [LOGIN - PROSES] Memproses percobaan autentikasi (login) yang dikirim pengguna.
     * URL: POST /login
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. AUTENTIKASI KREDENSIAL:
        // Memeriksa email dan password via LoginRequest.
        // Fungsi authenticate() juga menangani pencegahan brute-force (Rate Limiting).
        // Jika email/password salah, otomatis melempar ValidationException kembali ke form login.
        $request->authenticate();

        // 2. REGENERASI ID SESI:
        // Mencegah serangan Session Fixation dengan membuat ID session baru yang unik setelah login berhasil
        $request->session()->regenerate();

        // 3. PENGALIHAN (REDIRECT):
        // Mengarahkan pengguna ke URL tujuan awal sebelum diminta login (intended),
        // atau default ke dashboard admin jika tidak ada rute sebelumnya.
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * [LOGOUT - PROSES] Mengakhiri sesi pengguna yang sedang login (Logout).
     * URL: POST /logout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. LOGOUT DARI GUARD:
        // Menghapus data autentikasi pengguna saat ini dari guard 'web'
        Auth::guard('web')->logout();

        // 2. INVALIDASI SESI:
        // Menghapus semua data sesi yang tersimpan di server
        $request->session()->invalidate();

        // 3. REGENERASI TOKEN CSRF:
        // Memperbarui token CSRF baru untuk keamanan form berikutnya
        $request->session()->regenerateToken();

        // 4. PENGALIHAN (REDIRECT):
        // Mengarahkan pengguna kembali ke halaman utama (katalog publik)
        return redirect('/');
    }
}
