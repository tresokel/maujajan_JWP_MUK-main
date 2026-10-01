<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

// =========================================================================
// REGISTERED USER CONTROLLER (MANAJEMEN REGISTRASI PENGGUNA BARU)
// =========================================================================
// Controller ini menangani proses pendaftaran pengguna baru, hashing password,
// penyimpanan ke database tabel 'users', dan login otomatis setelah registrasi.
class RegisteredUserController extends Controller
{
    /**
     * [REGISTER - TAMPILAN] Menampilkan formulir pendaftaran pengguna baru.
     * URL: GET /register
     */
    public function create(): View
    {
        // Mengembalikan view formulir pendaftaran (resources/views/auth/register.blade.php)
        return view('auth.register');
    }

    /**
     * [REGISTER - PROSES] Memvalidasi data input dan mendaftarkan pengguna baru ke database.
     * URL: POST /register
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. VALIDASI INPUT REGISTRASI:
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], // Email harus unik di tabel users
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Password wajib cocok dengan input konfirmasi (password_confirmation)
        ]);

        // 2. SIMPAN DATA KE TABEL USERS:
        // Password dienkripsi menggunakan Hash::make() (algoritma Bcrypt) sebelum disimpan ke database
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. PEMICU EVENT REGISTERED:
        // Menjalankan event bahwa pengguna baru telah terdaftar (berguna untuk notifikasi verifikasi email jika aktif)
        event(new Registered($user));

        // 4. OTOMATIS LOGIN:
        // Pengguna langsung diautentikasi (login) ke dalam sistem setelah berhasil registrasi
        Auth::login($user);

        // 5. PENGALIHAN (REDIRECT):
        // Mengarahkan pengguna langsung ke halaman dashboard admin
        return redirect(route('dashboard', absolute: false));
    }
}
