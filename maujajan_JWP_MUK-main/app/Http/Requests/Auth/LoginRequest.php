<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

// =========================================================================
// LOGIN FORM REQUEST (VALIDASI & LOGIKA AUTENTIKASI KREDENSIAL)
// =========================================================================
// Form Request ini bertugas memvalidasi input email/password, menerapkan proteksi
// rate limiting (pencegahan brute-force login), dan mengeksekusi Auth::attempt().
class LoginRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        // Nilai true artinya request ini boleh dilakukan oleh siapa saja (tamu/guest)
        return true;
    }

    /**
     * Aturan validasi yang diterapkan pada input form login.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'], // Wajib diisi, format string email yang valid
            'password' => ['required', 'string'],          // Password wajib diisi
        ];
    }

    /**
     * Mencoba mengotentikasi kredensial pengguna ke database.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        // 1. Periksa apakah user telah melebihi batas percobaan login (terkena Rate Limit)
        $this->ensureIsNotRateLimited();

        // 2. Coba cocokkan kredensial email & password dengan tabel 'users' menggunakan Auth::attempt()
        //    Password yang dimasukkan akan di-hash dan dicocokkan otomatis dengan hash Bcrypt di database.
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Jika login gagal (email atau password tidak cocok):
            // Catat 1 kali kegagalan ke hitungan rate limiter
            RateLimiter::hit($this->throttleKey());

            // Lempar error validasi yang akan ditampilkan di view (auth.failed)
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // 3. Jika login berhasil, reset/hapus histori percobaan login yang gagal
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Memastikan request login tidak terkena rate limiting (proteksi serangan Brute-Force).
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Jika belum mencapai batas 5 kali percobaan gagal berturut-turut, lanjutkan
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        // Pemicu event lockout saat percobaan melebihi ambang batas
        event(new Lockout($this));

        // Hitung sisa detik hingga pengguna diizinkan mencoba login kembali
        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Kembalikan pesan error bahwa akun terkunci sementara karena terlalu banyak mencoba
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Menghasilkan key unik throttle berbasis kombinasi email pengguna dan alamat IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
