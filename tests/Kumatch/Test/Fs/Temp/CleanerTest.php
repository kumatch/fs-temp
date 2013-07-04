<?php

namespace Kumatch\Test\Fs\Temp;

use Kumatch\Fs\Temp\Cleaner;

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    protected $skelton;

    protected $top;
    protected $dirs = array();
    protected $files = array();

    protected function setUp()
    {
        parent::setUp();

        $this->top = sys_get_temp_dir() . "/kumatch-test-fs-temp-cleaner-" . microtime(true);
        $this->create($this->top);


    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->clean($this->top);
    }


    protected function create($top)
    {
        $this->dirs = array(
            $top . '/1a',
            $top . '/1a/2a',
            $top . '/1a/2a/3a',
            $top . '/1a/2a/3b',
            $top . '/1a/2b',
            $top . '/1a/2b/3a',

            $top . '/1b',
            $top . '/1b/2a',
        );

        foreach ($this->dirs as $dir) {
            mkdir($dir, 0755, true);
        }

        $this->files = array(
            $top . '/foo.txt',
            $top . '/bar.txt',

            $top . '/1a/2a.txt',
            $top . '/1a/2b.txt',
            $top . '/1a/2a/3a.txt',
            $top . '/1a/2a/3b.txt',
        );

        foreach ($this->files as $file) {
            touch($file);
        }
    }

    protected function clean($dir)
    {
        if (file_exists($dir) && is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        $this->clean($dir."/".$object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }


    public function testファイルを指定するとファイル削除処理が実行される()
    {
        $file1 = $this->top . '/foo.txt';
        $file2 = $this->top . '/bar.txt';

        $this->assertTrue(file_exists($file1));
        $this->assertTrue(file_exists($file2));

        Cleaner::clean($file1);

        $this->assertFalse(file_exists($file1));
        $this->assertTrue(file_exists($file2));
    }

    public function testディレクトリを指定すると再帰的なディレクトリ削除処理が実行される()
    {
        $dir1 = $this->top . '/1a';
        $dir2 = $this->top . '/1b';

        $this->assertTrue(file_exists($dir1));
        $this->assertTrue(file_exists($dir2));

        Cleaner::clean($dir1);

        $this->assertFalse(file_exists($dir1));
        $this->assertTrue(file_exists($dir2));
    }
}
