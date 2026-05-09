<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Requests\Api\Provider\RegisterRequest;
use App\Http\Resources\ProviderResource;
use App\Models\ServiceProvider;
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
     * Send OTP to provider's phone number.
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $this->otpService->generateAndSend($request->phone, 'provider');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'data' => [
                'expires_in' => config('otp.expiry_minutes', 10) * 60, // in seconds
            ],
        ]);
    }

    /**
     * Verify OTP and authenticate provider.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        if (!$this->otpService->verify($request->phone, $request->otp, 'provider')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
                'data' => [
                    'attempts_left' => $this->otpService->getAttemptsLeft($request->phone, 'provider'),
                ],
            ], 422);
        }

        // Find or create provider
        $provider = ServiceProvider::firstOrCreate(
            ['phone' => $request->phone],
            [
                'phone_verified_at' => now(),
                'status' => 'pending',
            ]
        );

        $isNew = $provider->wasRecentlyCreated;

        // Update phone_verified_at if provider already exists
        if (!$provider->wasRecentlyCreated) {
            $provider->update(['phone_verified_at' => now()]);
        }

        // Check if provider is suspended or rejected
        if (in_array($provider->status, ['suspended', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => "Your account has been {$provider->status}. Please contact support.",
            ], 403);
        }

        // Create Sanctum token
        $token = $provider->createToken('provider-token', ['provider'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'provider' => new ProviderResource($provider),
                'is_new' => $isNew,
            ],
        ]);
    }

    /**
     * Register provider with details.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $provider = $request->user();

        // Only allow registration for pending providers
        if ($provider->status !== 'pending' || $provider->name) {
            return response()->json([
                'success' => false,
                'message' => 'Registration already completed',
            ], 422);
        }

        $data = $request->only(['name', 'vehicle_type', 'service_speciality', 'language']);

        // Handle ID document upload
        if ($request->hasFile('id_document')) {
            $path = $request->file('id_document')->store("provider-docs/{$provider->id}", 'local');
            $data['id_document_path'] = $path;
        }

        $provider->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Registration submitted successfully. Awaiting admin approval.',
            'data' => [
                'provider' => new ProviderResource($provider->fresh()),
            ],
        ]);
    }

    /**
     * Get authenticated provider.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'provider' => new ProviderResource($request->user()),
            ],
        ]);
    }

    /**
     * Logout provider.
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
