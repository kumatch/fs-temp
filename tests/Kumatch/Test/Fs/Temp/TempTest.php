<?php

namespace Kumatch\Test\Fs\Temp;

use Kumatch\Fs\Temp\Temp;

class TempTest extends \PHPUnit_Framework_TestCase
{
    protected $skelton;


    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testコンストラクタでディレクトリを指定しない場合はシステム値を利用する()
    {
        $temp = new Temp();

        $this->assertAttributeEquals(sys_get_temp_dir(), 'directory', $temp);
    }

    public function testコンストラクタでディレクトリを指定した場合はその値を利用する()
    {
        $directory = __DIR__;
        $temp = new Temp($directory);

        $this->assertAttributeEquals($directory, 'directory', $temp);
    }

    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function testコンストラクタでファイルパスを指定した場合は例外が発生()
    {
        $temp = new Temp(__FILE__);
    }

    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function testコンストラクタで存在しないパスを指定した場合は例外が発生()
    {
        $temp = new Temp("/path/to/invalid");
    }


    public function test一時ディレクトリ作成のための生成インターフェースを用意する()
    {
        $temp = new Temp();
        $dir = $temp->dir();

        $this->assertInstanceOf('\Kumatch\Fs\Temp\DirectoryCreator', $dir);
        $this->assertAttributeEquals(sys_get_temp_dir(), 'directory', $dir);


        $temp = new Temp(__DIR__);
        $dir = $temp->dir();

        $this->assertInstanceOf('\Kumatch\Fs\Temp\DirectoryCreator', $dir);
        $this->assertAttributeEquals(__DIR__, 'directory', $dir);
    }

    public function test一時ファイル作成のための生成インターフェースを用意する()
    {
        $temp = new Temp();
        $file = $temp->file();

        $this->assertInstanceOf('\Kumatch\Fs\Temp\FileCreator', $file);
        $this->assertAttributeEquals(sys_get_temp_dir(), 'directory', $file);


        $temp = new Temp(__DIR__);
        $file = $temp->file();

        $this->assertInstanceOf('\Kumatch\Fs\Temp\FileCreator', $file);
        $this->assertAttributeEquals(__DIR__, 'directory', $file);
    }
}
