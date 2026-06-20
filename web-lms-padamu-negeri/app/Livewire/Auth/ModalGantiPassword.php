<?php

namespace App\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;

class ModalGantiPassword extends Component
{
    public bool $terbuka = false;

    public string $passwordBaru       = '';
    public string $konfirmasiPassword = '';

    public function mount(): void
    {
        if (auth()->check() && session('prompt_ganti_password', false)) {
            $this->terbuka = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'passwordBaru'       => 'required|string|min:6',
            'konfirmasiPassword' => 'required|string|same:passwordBaru',
        ], [
            'passwordBaru.required'       => 'Password baru wajib diisi.',
            'passwordBaru.min'            => 'Password minimal 6 karakter.',
            'konfirmasiPassword.required' => 'Konfirmasi password wajib diisi.',
            'konfirmasiPassword.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        auth()->user()->update([
            'password'           => $this->passwordBaru,
            'is_change_password' => true,
        ]);

        session()->forget('prompt_ganti_password');
        $this->terbuka = false;
        $this->reset('passwordBaru', 'konfirmasiPassword');
    }

    public function skip(): void
    {
        session()->forget('prompt_ganti_password');
        $this->terbuka = false;
    }

    public function render(): View
    {
        return view('livewire.auth.modal-ganti-password');
    }
}
