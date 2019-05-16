<?php
declare(strict_types=1);

namespace BlackBonjourBench\Stdlib\Util;

use BlackBonjour\Stdlib\Util\Map;
use BlackBonjourBench\Stdlib\Util\Assets\MapV2;

/**
 * @author    Erick Dyck <info@erickdyck.de>
 * @since     16.05.2019
 * @package   BlackBonjourBench\Stdlib\Util
 * @copyright Copyright (c) 2019 Erick Dyck
 *
 * @Revs(1000)
 * @Iterations(5)
 */
class MapReadBench
{
    /** @var Map */
    private $map;

    /** @var MapV2 */
    private $mapV2;

    public function __construct()
    {
        $this->map   = (new Map)->put('key', 'value');
        $this->mapV2 = (new MapV2)->put('key', 'value');
    }

    public function benchGet()
    {
        $this->map->get('key');
    }

    public function benchGetV2()
    {
        $this->mapV2->get('key');
    }

    public function benchOffsetGet()
    {
        $this->map->offsetGet('key');
    }

    public function benchOffsetGetV2()
    {
        $this->mapV2->offsetGet('key');
    }
}
