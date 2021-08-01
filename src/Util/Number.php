<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     27.04.2018
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Number
{
    public const ORDINAL_ST = 'st';
    public const ORDINAL_ND = 'nd';
    public const ORDINAL_RD = 'rd';
    public const ORDINAL_TH = 'th';

    public static function ordinal(string|float|int $number): string
    {
        $number = (int) abs((int) $number);

        if ($number === 11 || $number === 12 || $number === 13) {
            return static::ORDINAL_TH;
        }

        return match ($number % 10) {
            1       => static::ORDINAL_ST,
            2       => static::ORDINAL_ND,
            3       => static::ORDINAL_RD,
            default => static::ORDINAL_TH,
        };
    }

    public static function ordinalize(string|float|int $number): string
    {
        return $number . static::ordinal($number);
    }
}
