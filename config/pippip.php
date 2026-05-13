<?php

return [
    'default_commission' => env('PIPPIP_DEFAULT_COMMISSION', 20.0),
    'otp_expiry_minutes' => env('PIPPIP_OTP_EXPIRY', 10),
    'otp_max_attempts' => env('PIPPIP_OTP_MAX_ATTEMPTS', 3),
    'dispatch_radius_km' => env('PIPPIP_DISPATCH_RADIUS', 10),
    'dispatch_max_providers' => env('PIPPIP_MAX_PROVIDERS', 5),
    'offer_expiry_seconds' => env('PIPPIP_OFFER_EXPIRY', 30),
    'currency' => env('PIPPIP_CURRENCY', 'IQD'),
];
