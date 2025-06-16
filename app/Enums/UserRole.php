<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER_COMPANY = 'user_company';
    case ADMIN_COMPANY = 'admin_company';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}