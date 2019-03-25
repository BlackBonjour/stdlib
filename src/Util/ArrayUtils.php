<?php
declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     25.03.2019
 * @package   BlackBonjour\Stdlib\Util
 * @copyright Copyright (c) 2019 Erick Dyck
 */
class ArrayUtils
{
    /**
     * Flattens a two dimensional array to an one dimensional array.
     *
     * @param array $input
     * @return array
     */
    public static function flatten(array $input): array
    {
        return array_merge(...array_values($input));
    }
}
