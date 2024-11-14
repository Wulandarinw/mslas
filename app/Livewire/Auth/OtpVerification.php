<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\OTPService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpVerification extends Component
{
    public $otp = ['', '', '', '', '', ''];
    public $email;
    public $resendDisabled = false;
    public $countdown = 60;

    public function mount()
    {
        $this->email = session('email');
        if (!$this->email) {
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.auth.otp');
    }

    public function verify()
    {
        $this->validate([
            'otp.*' => 'required|integer|min:0|max:9',
        ], [
            'otp.*.required' => 'All OTP fields are required.',
            'otp.*.integer' => 'OTP must contain only numbers.',
            'otp.*.min' => 'Each OTP digit must be between 0 and 9.',
            'otp.*.max' => 'Each OTP digit must be between 0 and 9.',
        ]);

        $otpString = implode('', $this->otp);
        $user = Auth::user();

        if (!$user) {
            $this->addError('verification', 'User not found. Please login again.');
            return;
        }

        $otpService = new OTPService();
        
        if ($otpService->verifyOTP($user, $otpString)) {
            if ($user->email_verified_at === null) {
                $user->email_verified_at = Carbon::now();
                $user->save();
                
                return redirect()->intended()->with('status', 'Email successfully verified!');
            } else {
                return redirect()->route('seller.IDCard')->with('status', 'Email successfully verified!');
            }
        }

        $this->addError('verification', 'Invalid OTP code. Please try again.');
        $this->otp = ['', '', '', '', '', '']; // Reset OTP fields
    }

    public function resendOTP()
    {
        if ($this->resendDisabled) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            $this->addError('verification', 'User not found. Please login again.');
            return;
        }

        $otpService = new OTPService();
        $otpService->generateOTP($user);

        $this->resendDisabled = true;
        $this->countdown = 60;

        $this->dispatchBrowserEvent('otp-resent', ['message' => 'New OTP code has been sent to your email.']);
        $this->startCountdown();
    }

    private function startCountdown()
    {
        $this->dispatchBrowserEvent('start-countdown');
    }
}