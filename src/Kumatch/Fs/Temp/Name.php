<?php

namespace Kumatch\Fs\Temp;

class Name
{
    public static function create($prefix = null, $suffix = null)
    {
        return implode(array(
            is_null($prefix) ? "tmp-" : $prefix,
            getmypid(),
            base_convert(mt_rand(1, 0x1000000000), 10, 36),
            is_null($suffix) ? "" : $suffix
        ));
    }
}