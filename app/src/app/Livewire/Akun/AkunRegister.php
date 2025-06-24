<?php

namespace App\Livewire\Akun;

use Livewire\Component;
use App\Models\Akun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunRegister extends Component
{
    public $name = '';
    public $email = '';
    public $whatsapp_number = '';
    public $password = '';
    public $password_confirmation = '';
    public $showPassword = false;
    public $showConfirmPassword = false;

    public function toggleShowPassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function toggleShowConfirmPassword()
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }

    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email',
        'whatsapp_number' => 'required|string|max:20|unique:akuns,whatsapp_number',
        'password' => 'required|string|min:6',
        'password_confirmation' => 'required|same:password',
    ];


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:akuns,email',
            'whatsapp_number' => 'required|string|max:20|unique:akuns,whatsapp_number',
            'password' => 'required|min:6|confirmed',
        ]);

        $akun = Akun::create([
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp_number' => $this->whatsapp_number,
            'password' => Hash::make($this->password),
        ]);

        Auth::guard('akun')->login($akun);

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.akun.register');
    }
}

