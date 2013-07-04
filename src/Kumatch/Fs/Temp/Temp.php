<?php

namespace Kumatch\Fs\Temp;

use Kumatch\Fs\Temp\DirectoryCreator;
use Kumatch\Fs\Temp\FileCreator;
use Kumatch\Fs\Temp\Exception\Exception;

class Temp
{
    /** @var  string */
    protected $directory;

    /**
     * @param string|null $directory
     * @throws Exception
     */
    public function __construct($directory = null)
    {
        if ($directory) {
            if (!file_exists($directory) || !is_dir($directory)) {
                throw new Exception('invalid directory');
            }

            $this->directory = realpath($directory);
        } else {
            $this->directory = sys_get_temp_dir();
        }
    }


    /**
     * @return DirectoryCreator
     */
    public function dir()
    {
        return new DirectoryCreator($this->directory);
    }

    /**
     * @return FileCreator
     */
    public function file()
    {
        return new FileCreator($this->directory);
    }
}