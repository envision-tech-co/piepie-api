<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pippip:expire-stale-offers')->everyThirtySeconds();
Schedule::command('pippip:mark-stale-bookings')->everyFiveMinutes();
