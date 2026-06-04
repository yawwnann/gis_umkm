<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case FieldOfficer = 'field_officer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::FieldOfficer => 'Petugas Lapangan',
        };
    }
}