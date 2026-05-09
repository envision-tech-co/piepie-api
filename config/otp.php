<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OTP (One-Time Password) functionality.
    |
    */

    'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 3),
    'length' => env('OTP_LENGTH', 6),

];
