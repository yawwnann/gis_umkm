<?php

namespace App\Enums;

enum PotentialLevel: int
{
    case Tinggi = 1;
    case Sedang = 2;
    case Rendah = 3;

    public function label(): string
    {
        return match ($this) {
            self::Tinggi => 'Potensi Tinggi',
            self::Sedang => 'Potensi Sedang',
            self::Rendah => 'Potensi Rendah',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Tinggi => '#22c55e',   // green
            self::Sedang => '#eab308',   // yellow
            self::Rendah => '#ef4444',   // red
        };
    }
}