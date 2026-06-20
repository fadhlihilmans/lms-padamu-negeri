<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Login')]
class LoginForm extends Component
{
    public string $username = '';
    public string $password = '';

    public function login(): void
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            $this->addError('username', 'Username atau password salah.');
            return;
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $this->addError('username', 'Akun Anda tidak aktif. Hubungi Admin.');
            return;
        }

        session()->regenerate();

        if (! $user->is_change_password) {
            session(['prompt_ganti_password' => true]);
        }

        $this->redirect(route('dashboard'), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.auth.login-form');
    }
}
