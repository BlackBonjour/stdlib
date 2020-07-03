<?php

declare(strict_types=1);

namespace BlackBonjour\Stdlib\Util;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     25.03.2019
 * @copyright Copyright (c) 2019 Erick Dyck
 */
class ArrayUtils
{
    /**
     * Flattens a two dimensional array to an one dimensional array.
     */
    public static function flatten(array $input): array
    {
        if (empty($input)) {
            return [];
        }

        return array_merge(...array_values($input));
    }
}
