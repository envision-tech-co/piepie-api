<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS sending functionality.
    |
    */

    'driver' => env('SMS_DRIVER', 'log'),
    'from' => env('SMS_FROM', 'PipPip'),

];
