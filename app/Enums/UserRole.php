<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case STAFF = 'staff';
    case MANAGER = 'manager';
}
