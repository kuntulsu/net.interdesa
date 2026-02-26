<?php

namespace App;

enum PaymentMethodEnum: int
{
    case Tunai = 0;
    case Transfer = 1;

    public static function toArray(): array
    {
        $cases = [];
        foreach (self::cases() as $case) {
            $cases[$case->name] = $case->value;
        }

        return $cases;
    }
}
