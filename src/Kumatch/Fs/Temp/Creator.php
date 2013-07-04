<?php

namespace Kumatch\Fs\Temp;

use Kumatch\Fs\Temp\Name;
use Kumatch\Fs\Temp\Exception\Exception;

abstract class Creator
{
    /** @var  string */
    protected $directory;

    /** @var  string */
    protected $prefix = "tmp-";
    /** @var  string */
    protected $suffix = "";
    /** @var  int */
    protected $mode = 0700;

    /** @var string  */
    protected $ngPattern = '!\.\.!';

    /**
     * @param string|null $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    /**
     * @param string $prefix
     * @return $this
     * @throws Exception
     */
    public function prefix($prefix)
    {
        if (preg_match($this->ngPattern, $prefix)) {
            throw new Exception('invalid arguments');
        }

        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     * @throws Exception
     */
    public function suffix($suffix)
    {
        if (preg_match($this->ngPattern, $suffix)) {
            throw new Exception('invalid arguments');
        }

        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @param $mode
     * @return $this
     */
    public function mode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function create()
    {
        $path = $this->createUniquePathName($this->prefix, $this->suffix);

        if (!$path) {
            throw new Exception(sprintf('cannot create in %s', $this->directory));
        }

        if (!$this->_create($path, $this->mode)) {
            throw new Exception(sprintf('cannot create in %s', $this->directory));
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
    abstract protected function _create($path, $mode);

}