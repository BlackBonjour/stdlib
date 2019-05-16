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
class MapArrayAccessBench
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

    public function benchOffsetExists()
    {
        isset($this->map['key']);
    }

    public function benchOffsetExistsV2()
    {
        isset($this->mapV2['key']);
    }

    public function benchOffsetGet()
    {
        $this->map['key'];
    }

    public function benchOffsetGetV2()
    {
        $this->mapV2['key'];
    }

    public function benchOffsetSet()
    {
        $this->map['foo'] = 'bar';
    }

    public function benchOffsetSetV2()
    {
        $this->mapV2['foo'] = 'bar';
    }

    public function benchOffsetUnset()
    {
        unset($this->map['key']);
    }

    public function benchOffsetUnsetV2()
    {
        unset($this->mapV2['key']);
    }
}
