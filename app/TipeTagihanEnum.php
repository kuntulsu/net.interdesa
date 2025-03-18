<?php

namespace App;

enum TipeTagihanEnum: string
{
    case BULANAN = "Bulanan";
    case PSB = "Biaya Pemasangan Baru";
    case LAINYA = "Lain Lain"; 

    public static function toArray(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[$case->name] = $case->value;
        }

        return $cases;
    }

    public static function to(string $to)
    {
        foreach (self::cases() as $case) {
            if ($case->name === $to) {
                return $case;
            }
        }
        return null;
    }
}
