<?php

namespace Kumatch\Fs\Temp;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Cleaner
{
    public static function clean($path)
    {
        if (file_exists($path)) {
            if (is_file($path)) {
                static::removeFile($path);
            } else if (is_dir($path)) {
                static::removeDirRecursive($path);
            }
        }
    }

    protected static function removeFile($path)
    {
        return @unlink($path);
    }

    protected static function removeDirRecursive($path)
    {
        $result = true;

        $directory = new \RecursiveDirectoryIterator($path,
          \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
        );
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $fileInfo) {
            /** @var $fileInfo \SplFileInfo */
            if ($fileInfo->isDir()) {
                $pathname = $fileInfo->getPathname();

                if (count( glob($pathname . '/*'))) {
                    $result = false;
                    break;
                }

                if (!@rmdir($pathname)) {
                    $result = false;
                    break;
                }
            } else {
                if (!@unlink($fileInfo->getPathname())) {
                    $result = false;
                    break;
                }
            }
        }

        if (!$result) {
            return false;
        }

        return @rmdir($path);
    }
}