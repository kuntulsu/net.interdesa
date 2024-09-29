<?php

namespace App;

enum TipeTransaksi: string
{
    case KELUAR = "KELUAR";
    case MASUK = "MASUK";

    public static function toArray(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[$case->name] = $case->value;
        }

        return $cases;
    }
}
