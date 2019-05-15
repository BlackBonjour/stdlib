<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

use TypeError;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     27.04.2018
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2018 Erick Dyck
 */
class Number
{
    public const ORDINAL_ST = 'st';
    public const ORDINAL_ND = 'nd';
    public const ORDINAL_RD = 'rd';
    public const ORDINAL_TH = 'th';

    /**
     * @param string|float|int $number Numeric value
     */
    public static function ordinal($number): string
    {
        if (is_numeric($number) === false) {
            throw new TypeError('Number must be of type string, float or integer!');
        }

        $number = abs((int) $number);

        if ($number === 11 || $number === 12 || $number === 13) {
            return self::ORDINAL_TH;
        }

        switch ($number % 10) {
            case 1:
                return self::ORDINAL_ST;
            case 2:
                return self::ORDINAL_ND;
            case 3:
                return self::ORDINAL_RD;
        }

        return self::ORDINAL_TH;
    }

    /**
     * @param string|float|int $number Numeric value
     */
    public static function ordinalize($number): string
    {
        return $number . self::ordinal($number);
    }
}
