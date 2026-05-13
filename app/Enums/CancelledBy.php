<?php

namespace App\Enums;

enum CancelledBy: string
{
    case Customer = 'customer';
    case Provider = 'provider';
    case Admin = 'admin';
}
