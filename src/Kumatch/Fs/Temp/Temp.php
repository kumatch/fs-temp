<?php

namespace Kumatch\Fs\Temp;

use Kumatch\Fs\Temp\Name;
use Kumatch\Fs\Temp\Exception\Exception;
use SplFileObject;

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
     * @param int $mode
     * @param string|null $prefix
     * @param string|null $suffix
     * @return string
     * @throws Exception
     */
    public function dir($mode = 0700, $prefix = null, $suffix = null)
    {
        $ngPattern = '!\.\.!';
        if (preg_match($ngPattern, $prefix) || preg_match($ngPattern, $suffix)) {
            throw new Exception('invalid arguments');
        }

        $path = $this->createUniquePathName($prefix, $suffix);

        if (!$path) {
            throw new Exception(sprintf('cannot create directory in %s', $this->directory));
        }

        if (!$this->mkdir($path, $mode)) {
            throw new Exception(sprintf('cannot create directory in %s', $this->directory));
        }

        return $path;
    }

    public function file($mode = 0600, $prefix = null, $suffix = null)
    {
        $ngPattern = '!\.\.!';
        if (preg_match($ngPattern, $prefix) || preg_match($ngPattern, $suffix)) {
            throw new Exception('invalid arguments');
        }

        $path = $this->createUniquePathName($prefix, $suffix);

        if (!$path) {
            throw new Exception(sprintf('cannot create file in %s', $this->directory));
        }

        if (!$this->touch($path, $mode)) {
            throw new Exception(sprintf('cannot create file in %s', $this->directory));
        }

        return $path;
    }


    protected function createUniquePathName($prefix = null, $suffix = null)
    {
        $stock = 100;
        $result = null;

        while ($stock > 0) {
            --$stock;

            $name = Name::create($prefix, $suffix);
            $path = sprintf('%s/%s', $this->directory, $name);

            if (!file_exists($path)) {
                $result = $path;
                break;
            }
        }

        return $result;
    }

    /**
     * @param $path
     * @param $mode
     * @return bool
     */
    protected function mkdir($path, $mode)
    {
        return mkdir($path, $mode, true);
    }

    /**
     * @param $path
     * @param $mode
     * @return bool
     */
    protected function touch($path, $mode)
    {
        try {
            $file = new SplFileObject($path, 'x+');

            return chmod($path, $mode);
        } catch (\Exception $e) {
            return false;
        }
    }
}