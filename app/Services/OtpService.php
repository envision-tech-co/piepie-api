<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate and send OTP code to the given phone number.
     *
     * @param string $phone The phone number in E.164 format
     * @param string $type The type of OTP (customer or provider)
     * @return OtpCode
     */
    public function generateAndSend(string $phone, string $type): OtpCode
    {
        // Invalidate any previous unused OTPs for this phone and type
        OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        // Generate a random 6-digit OTP
        $code = str_pad((string) random_int(0, 999999), config('otp.length', 6), '0', STR_PAD_LEFT);

        // Create OTP record with hashed code
        $otpCode = OtpCode::create([
            'phone' => $phone,
            'otp_code' => Hash::make($code),
            'type' => $type,
            'expires_at' => now()->addMinutes(config('otp.expiry_minutes', 10)),
            'attempts' => 0,
        ]);

        // Log OTP for development (in production, this would send SMS)
        $this->sendOtp($phone, $code);

        return $otpCode;
    }

    /**
     * Verify the OTP code for the given phone number.
     *
     * @param string $phone The phone number in E.164 format
     * @param string $code The OTP code to verify
     * @param string $type The type of OTP (customer or provider)
     * @return bool
     */
    public function verify(string $phone, string $code, string $type): bool
    {
        $otpRecord = OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->valid()
            ->latest()
            ->first();

        if (!$otpRecord) {
            return false;
        }

        // Check if max attempts exceeded
        if ($otpRecord->attempts >= config('otp.max_attempts', 3)) {
            return false;
        }

        // Increment attempts
        $otpRecord->increment('attempts');

        // Verify the OTP code
        if (Hash::check($code, $otpRecord->otp_code)) {
            // Mark as used
            $otpRecord->update(['used_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Get the number of attempts left for the given phone number.
     *
     * @param string $phone The phone number in E.164 format
     * @param string $type The type of OTP (customer or provider)
     * @return int
     */
    public function getAttemptsLeft(string $phone, string $type): int
    {
        $otpRecord = OtpCode::where('phone', $phone)
            ->where('type', $type)
            ->valid()
            ->latest()
            ->first();

        if (!$otpRecord) {
            return config('otp.max_attempts', 3);
        }

        return max(0, config('otp.max_attempts', 3) - $otpRecord->attempts);
    }

    /**
     * Send OTP via configured driver (log for development, SMS for production).
     *
     * @param string $phone
     * @param string $code
     * @return void
     */
    protected function sendOtp(string $phone, string $code): void
    {
        $driver = config('sms.driver', 'log');

        if ($driver === 'log') {
            Log::info("OTP for {$phone}: {$code}");
        } else {
            // TODO: Implement real SMS sending logic here
            // Example: SMS::send($phone, "Your PipPip OTP is: {$code}");
        }
    }
}
