<?php

namespace App\Livewire\Profil;

use App\Services\ErrorLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app', ['pageTitle' => 'Profil Saya'])]
#[Title('Profil Saya')]
class ProfilSaya extends Component
{
    /** Kontak yang boleh diubah pengguna sendiri. */
    public string $no_hp = '';

    /** Ganti password. */
    public string $passwordLama       = '';
    public string $passwordBaru       = '';
    public string $konfirmasiPassword = '';

    public function mount(): void
    {
        $profil = $this->profil();
        $this->no_hp = $profil?->no_hp ?? '';
    }

    /** Model guru/peserta_didik milik user, atau null (admin). */
    private function profil()
    {
        $user = Auth::user();

        return $user?->guru ?? $user?->pesertaDidik;
    }

    public function saveKontak(): void
    {
        $profil = $this->profil();

        if (! $profil) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Akun ini tidak memiliki data kontak.']);
            return;
        }

        $this->validate(
            ['no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]*$/']],
            [
                'no_hp.max'   => 'Nomor HP maksimal 20 karakter.',
                'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, +, atau -.',
            ],
        );

        try {
            $profil->update(['no_hp' => trim($this->no_hp) ?: null]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Kontak berhasil diperbarui.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Update Kontak Profil', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    /** Bersihkan input & error saat modal ditutup/dibatalkan. */
    public function resetPasswordForm(): void
    {
        $this->reset('passwordLama', 'passwordBaru', 'konfirmasiPassword');
        $this->resetValidation(['passwordLama', 'passwordBaru', 'konfirmasiPassword']);
    }

    public function gantiPassword(): void
    {
        $this->validate(
            [
                'passwordLama'       => ['required', 'string'],
                'passwordBaru'       => ['required', 'string', 'min:3', 'different:passwordLama'],
                'konfirmasiPassword' => ['required', 'string', 'same:passwordBaru'],
            ],
            [
                'passwordLama.required'       => 'Password saat ini wajib diisi.',
                'passwordBaru.required'       => 'Password baru wajib diisi.',
                'passwordBaru.min'            => 'Password baru minimal 3 karakter.',
                'passwordBaru.different'      => 'Password baru harus berbeda dari password saat ini.',
                'konfirmasiPassword.required' => 'Konfirmasi password wajib diisi.',
                'konfirmasiPassword.same'     => 'Konfirmasi password tidak cocok.',
            ],
        );

        if (! Hash::check($this->passwordLama, Auth::user()->password)) {
            $this->addError('passwordLama', 'Password saat ini salah.');
            return;
        }

        try {
            Auth::user()->update([
                'password'           => $this->passwordBaru,
                'is_change_password' => true,
            ]);

            $this->reset('passwordLama', 'passwordBaru', 'konfirmasiPassword');
            $this->dispatch('password-changed'); // tutup modal di sisi Alpine
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Password berhasil diubah.']);
        } catch (\Throwable $th) {
            app(ErrorLogService::class)->record('Ganti Password Profil', $th);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Terjadi kesalahan, silakan coba lagi.']);
        }
    }

    public function render(): View
    {
        $user = Auth::user();

        $role = match (true) {
            $user->hasRole('admin')          => 'admin',
            $user->hasRole('guru')           => 'guru',
            $user->hasRole('peserta_didik')  => 'peserta_didik',
            default                          => 'lainnya',
        };

        $guru = $role === 'guru' ? $user->guru : null;
        $pd   = $role === 'peserta_didik'
            ? $user->pesertaDidik()->with(['alamat', 'ortu'])->first()
            : null;

        return view('livewire.profil.profil-saya', [
            'user' => $user,
            'role' => $role,
            'guru' => $guru,
            'pd'   => $pd,
        ]);
    }
}
