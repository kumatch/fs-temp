<?php

namespace Kumatch\Fs\Temp;

use Kumatch\Fs\Temp\Creator;

class DirectoryCreator extends Creator
{
    /** @var  int */
    protected $mode = 0700;

    /**
     * @param $path
     * @param $mode
     * @return bool
     */
    protected function _create($path, $mode)
    {
        return mkdir($path, $mode, true);
    }
}