<?php

namespace Kumatch\Fs\Temp;

use Kumatch\Fs\Temp\Creator;
use SplFileObject;

class FileCreator extends Creator
{
    /** @var  int */
    protected $mode = 0600;

    /**
     * @param $path
     * @param $mode
     * @return bool
     */
    protected function _create($path, $mode)
    {
        try {
            $file = new SplFileObject($path, 'x+');

            return chmod($path, $mode);
        } catch (\Exception $e) {
            return false;
        }
    }
}