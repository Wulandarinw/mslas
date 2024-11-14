<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Register')]
class RegisterPage extends Component
{

    public $name;
    public $email;
    public $password;

    //register user
    public function save()
    {
        $this->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        //save to database
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $firstName = strtok($user->name, ' ');

        Customer::create([
            'FName' => $firstName,
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        //login user
        Auth::login($user);

        session(['email' => $this->email]);

        // Generate OTP sebelum redirect
        $otpService = new OtpService();
        $otpService->generateOTP($user);

        // Gunakan salah satu dari opsi redirect berikut:

        // Opsi 1: Redirect Livewire
        return redirect()->route('otp.verify');
    }
    public function render()
    {
        return view('livewire.auth.register-page');
    }
}
