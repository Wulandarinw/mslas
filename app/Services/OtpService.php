<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generateOTP(User $user)
    {
        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(1);

        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your OTP Code');
        });

        return $otp;
    }

    public function verifyOTP(User $user, $otp)
    {
        $otpEntry = Otp::where('user_id', $user->id)
                       ->where('otp', $otp)
                       ->where('expires_at', '>', Carbon::now())
                       ->first();

        return $otpEntry !== null;
    }
}
