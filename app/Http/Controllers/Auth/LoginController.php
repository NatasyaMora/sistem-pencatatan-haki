<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Autentikasi Admin secara lokal dan mahasiswa/dosen melalui Bridge API.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'username.required' => 'NIM/NIP/NIK atau email Admin wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = trim($validated['username']);
        $password = $validated['password'];
        $remember = $request->boolean('remember');

        /*
         * 1. Login Admin tetap menggunakan database lokal HAKIKU.
         * Kredensial Admin tidak dikirim ke API kampus.
         */
        $localAdmin = User::query()
            ->where('email', $identifier)
            ->where('role', 'admin')
            ->first();

        if ($localAdmin) {
            if (Auth::attempt([
                'email' => $localAdmin->email,
                'password' => $password,
            ], $remember)) {
                $request->session()->regenerate();

                return redirect('/admin/dashboard');
            }

            return back()->withErrors([
                'username' => 'Email Admin atau password yang dimasukkan salah.',
            ])->onlyInput('username');
        }

        /*
         * 2. Login mahasiswa/dosen melalui Bridge API.
         */
        $baseUrl = rtrim(
            (string) config('services.bridge.base_url'),
            '/'
        );

        $timeout = (int) config('services.bridge.timeout', 15);

        if ($baseUrl === '') {
            return back()->withErrors([
                'username' => 'Konfigurasi API kampus belum tersedia.',
            ])->onlyInput('username');
        }

        try {
            $loginResponse = Http::asForm()
                ->acceptJson()
                ->timeout($timeout)
                ->post($baseUrl . '/api/v2/portal/login', [
                    'username' => $identifier,
                    'password' => $password,
                ]);
        } catch (ConnectionException $exception) {
            return back()->withErrors([
                'username' => 'API kampus tidak dapat dihubungi. Periksa koneksi internet dan alamat API.',
            ])->onlyInput('username');
        }

        $loginJson = $loginResponse->json();
        $loginData = is_array($loginJson)
            ? ($loginJson['data'] ?? null)
            : null;

        $token = is_array($loginData)
            ? ($loginData['token'] ?? null)
            : null;

        if (
            !$loginResponse->successful() ||
            !is_array($loginJson) ||
            ($loginJson['status'] ?? false) !== true ||
            !is_array($loginData) ||
            !is_string($token) ||
            trim($token) === ''
        ) {
            if ($loginResponse->status() === 404) {
                $message = 'Endpoint login API kampus tidak ditemukan. Periksa konfigurasi API dengan Pustikom.';
            } elseif ($loginResponse->serverError()) {
                $message = 'Layanan API kampus sedang bermasalah. Silakan coba kembali nanti.';
            } else {
                $message = 'Login gagal. Periksa NIM/NIP/NIK dan password Portal Akademik.';
            }

            return back()->withErrors([
                'username' => $message,
            ])->onlyInput('username');
        }

        /*
         * 3. HAKIKU menerima role mahasiswa dan dosen.
         * Role Admin tetap dikelola secara lokal.
         */
        $apiRole = strtolower(trim(
            (string) ($loginData['role'] ?? '')
        ));

        if (!in_array($apiRole, ['mahasiswa', 'dosen'], true)) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Jenis akun dari API kampus tidak didukung oleh HAKIKU.',
            ])->onlyInput('username');
        }

        /*
         * 4. Ambil profil menggunakan token API.
         */
        try {
            $profileResponse = Http::withToken($token)
                ->acceptJson()
                ->timeout($timeout)
                ->get($baseUrl . '/api/v2/profil');
        } catch (ConnectionException $exception) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Login API berhasil, tetapi profil kampus tidak dapat dihubungi. Silakan coba lagi.',
            ])->onlyInput('username');
        }

        $profileJson = $profileResponse->json();
        $profileData = is_array($profileJson)
            ? ($profileJson['data'] ?? null)
            : null;

        if (
            !$profileResponse->successful() ||
            !is_array($profileJson) ||
            ($profileJson['status'] ?? false) !== true ||
            !is_array($profileData)
        ) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Profil kampus tidak berhasil diperoleh. Silakan coba kembali.',
            ])->onlyInput('username');
        }

        /*
         * 5. Petakan identitas dari profil API.
         */
        $identityNumber = null;

        foreach (['nim', 'nip', 'nik'] as $field) {
            $value = trim((string) ($profileData[$field] ?? ''));

            if ($value !== '') {
                $identityNumber = $value;
                break;
            }
        }

        if (!$identityNumber) {
            $identityNumber = trim(
                (string) ($loginData['username'] ?? $identifier)
            );
        }

        $name = trim((string) (
            $profileData['nama']
            ?? $loginData['nama']
            ?? ''
        ));

        if ($name === '') {
            $name = $identityNumber;
        }

        /*
         * Email dibutuhkan karena kolom users.email wajib dan unik.
         * Prioritaskan email kampus, lalu email alternatif.
         */
        $email = null;

        foreach ([
            $profileData['email_kampus'] ?? null,
            $profileData['email'] ?? null,
        ] as $emailCandidate) {
            if (
                is_string($emailCandidate) &&
                filter_var(trim($emailCandidate), FILTER_VALIDATE_EMAIL)
            ) {
                $email = strtolower(trim($emailCandidate));
                break;
            }
        }

        if (!$email) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Profil kampus tidak menyediakan email yang valid. Hubungi Admin HAKIKU untuk pemeriksaan data.',
            ])->onlyInput('username');
        }

        /*
         * 6. Cocokkan pengguna dengan database HAKIKU.
         * Jangan menghubungkan akun berdasarkan email saja jika
         * identitasnya berbeda, karena dapat memindahkan kepemilikan HAKI.
         */
        $user = User::query()
            ->where('identity_number', $identityNumber)
            ->first();

        if ($user && $user->isAdmin()) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Identitas tersebut terhubung dengan akun Admin lokal dan tidak dapat dipakai untuk login melalui API.',
            ])->onlyInput('username');
        }

        $emailOwner = User::query()
            ->where('email', $email)
            ->first();

        if ($emailOwner && (!$user || $emailOwner->id !== $user->id)) {
            $this->revokeBridgeToken($token);

            return back()->withErrors([
                'username' => 'Email kampus sudah terhubung dengan akun HAKIKU lain. Hubungi Admin untuk mencocokkan identitas.',
            ])->onlyInput('username');
        }

        /*
         * 7. Buat atau perbarui akun lokal.
         * Password acak hanya untuk kebutuhan kolom lokal.
         * Password Portal Akademik tidak pernah disimpan.
         */
        if (!$user) {
            $user = new User();
            $user->password = Str::random(64);
        }

        $user->name = $name;
        $user->email = $email;
        $user->identity_number = $identityNumber;
        $user->user_type = ucfirst($apiRole);
        $user->role = 'user';
        $user->save();

        /*
         * 8. Buat session Laravel dan simpan token di session server.
         */
        Auth::login($user, $remember);

        $request->session()->regenerate();
        $request->session()->put('bridge_api_token', $token);

        return redirect('/my-haki');
    }

    /**
     * Logout lokal sekaligus mencoba logout dari Bridge API.
     */
    public function logout(Request $request): RedirectResponse
    {
        $token = $request->session()->get('bridge_api_token');

        if (is_string($token) && $token !== '') {
            $this->revokeBridgeToken($token);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with(
            'success',
            'Anda telah berhasil logout.'
        );
    }

    /**
     * Mencoba mencabut token API tanpa menggagalkan logout lokal.
     */
    private function revokeBridgeToken(string $token): void
    {
        $baseUrl = rtrim(
            (string) config('services.bridge.base_url'),
            '/'
        );

        if ($baseUrl === '') {
            return;
        }

        try {
            Http::withToken($token)
                ->acceptJson()
                ->timeout(5)
                ->post($baseUrl . '/api/v2/logout');
        } catch (\Throwable $exception) {
            // Logout lokal tetap dilanjutkan meskipun API tidak tersedia.
        }
    }
}
