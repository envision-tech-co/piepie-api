<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Requests\Api\Customer\CompleteProfileRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Send OTP to customer's phone number.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $this->otpService->generateAndSend($request->phone, 'customer');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'expires_in' => config('otp.expiry_minutes', 10) * 60, // in seconds
            ],
        ]);
    }

    /**
     * Verify OTP and authenticate customer.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        if (!$this->otpService->verify($request->phone, $request->otp, 'customer')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'data' => [
                    'attempts_left' => $this->otpService->getAttemptsLeft($request->phone, 'customer'),
                ],
            ], 422);
        }

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['phone' => $request->phone],
            ['phone_verified_at' => now()]
        );

        $isNew = !$customer->wasRecentlyCreated && !$customer->name;

        // Update phone_verified_at if customer already exists
        if (!$customer->wasRecentlyCreated) {
            $customer->update(['phone_verified_at' => now()]);
        }

        // Create Sanctum token
        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'customer' => new CustomerResource($customer),
                'is_new' => $customer->wasRecentlyCreated || !$customer->name,
            ],
        ]);
    }

    /**
     * Complete customer profile.
     */
    public function completeProfile(CompleteProfileRequest $request): JsonResponse
    {
        $customer = $request->user();

        $data = $request->only(['name', 'language']);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($customer->profile_photo) {
                Storage::disk('local')->delete($customer->profile_photo);
            }

            $path = $request->file('profile_photo')->store('customer-photos', 'local');
            $data['profile_photo'] = $path;
        }

        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'customer' => new CustomerResource($customer->fresh()),
            ],
        ]);
    }

    /**
     * Get authenticated customer.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'customer' => new CustomerResource($request->user()),
            ],
        ]);
    }

    /**
     * Logout customer.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
