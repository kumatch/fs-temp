<?php

namespace Kumatch\Test\Fs\Temp;

use Kumatch\Fs\Temp\Temp;

class TempTest extends \PHPUnit_Framework_TestCase
{
    protected $skelton;

    /** @var  string */
    protected $tempPath;

    protected function setUp()
    {
        parent::setUp();

        $this->tempPath = sys_get_temp_dir() . "/kumatch-test-fs-tmp-" . microtime(true);
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->tempPath)) {
            if (is_dir($this->tempPath)) {
                rmdir($this->tempPath);
            } else if (is_file($this->tempPath)) {
                unlink($this->tempPath);
            }
        }
    }


    public function testコンストラクタでディレクトリを指定しない場合はシステム値を利用する()
    {
        $result = sys_get_temp_dir();
        $temp = new Temp();

        $this->assertAttributeEquals($result, 'directory', $temp);
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

    public function test一時ディレクトリを作成するとユニークな名称を持つディレクトリパスが用意される()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('mkdir'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('mkdir')
            ->will($this->returnValue(true));

        /** @var Temp $tmp */
        $dir = $tmp->dir();

        $this->assertRegExp(sprintf('!^%s/tmp\-[a-z0-9]+$!i', sys_get_temp_dir()), $dir);
    }

    public function test接頭辞および接尾辞を指定して一時ディレクトリを作成する()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('mkdir'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('mkdir')
            ->will($this->returnValue(true));

        /** @var Temp $tmp */
        $dir = $tmp->dir(700, "foo_", ".bar");

        $this->assertRegExp(sprintf('!^%s/foo_[a-z0-9]+.bar$!i', sys_get_temp_dir()), $dir);
    }

    public function test一時ディレクトリは通常ではモード700で作成される()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var Temp $tmp */
        $tmp->dir();

        $this->assertTrue(file_exists($this->tempPath));
        $this->assertTrue(is_dir($this->tempPath));

        $mode = substr(sprintf('%o', fileperms($this->tempPath)), -4);

        $this->assertEquals('0700', $mode);
    }

    public function test一時ディレクトリをモードを指定して作成する()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var Temp $tmp */
        $tmp->dir(0755);

        $this->assertTrue(file_exists($this->tempPath));
        $this->assertTrue(is_dir($this->tempPath));

        $mode = substr(sprintf('%o', fileperms($this->tempPath)), -4);

        $this->assertEquals('0755', $mode);
    }


    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function testユニークな一時ディレクトリパスを作成できなかった場合は例外が発生()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue(null));

        /** @var Temp $tmp */
        $tmp->dir();
    }

    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function test一時ディレクトリの作成に失敗した場合は例外が発生()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('mkdir'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('mkdir')
            ->will($this->returnValue(false));

        /** @var Temp $tmp */
        $tmp->dir();
    }





    public function test一時ファイルを作成するとユニークな名称を持つファイルパスが用意される()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('touch'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('touch')
            ->will($this->returnValue(true));

        /** @var Temp $tmp */
        $file = $tmp->file();

        $this->assertRegExp(sprintf('!^%s/tmp\-[a-z0-9]+$!i', sys_get_temp_dir()), $file);
    }

    public function test接頭辞および接尾辞を指定して一時ファイルを作成する()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('touch'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('touch')
            ->will($this->returnValue(true));

        /** @var Temp $tmp */
        $file = $tmp->file(600, "foo_", ".bar");

        $this->assertRegExp(sprintf('!^%s/foo_[a-z0-9]+.bar$!i', sys_get_temp_dir()), $file);
    }

    public function test一時ファイルは通常ではモード600で作成される()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var Temp $tmp */
        $tmp->file();

        $this->assertTrue(file_exists($this->tempPath));
        $this->assertTrue(is_file($this->tempPath));

        $mode = substr(sprintf('%o', fileperms($this->tempPath)), -4);

        $this->assertEquals('0600', $mode);
    }

    public function test一時ファイルをモードを指定して作成する()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var Temp $tmp */
        $tmp->file(0644);

        $this->assertTrue(file_exists($this->tempPath));
        $this->assertTrue(is_file($this->tempPath));

        $mode = substr(sprintf('%o', fileperms($this->tempPath)), -4);

        $this->assertEquals('0644', $mode);
    }


    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function testユニークな一時ファイルパスを作成できなかった場合は例外が発生()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue(null));

        /** @var Temp $tmp */
        $tmp->file();
    }

    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function test一時ファイルの作成に失敗した場合は例外が発生()
    {
        $tmp = $this->getMockBuilder('Kumatch\Fs\Temp\Temp')
            ->setConstructorArgs(array())
            ->setMethods(array('touch'))
            ->getMock();
        $tmp->expects($this->once())
            ->method('touch')
            ->will($this->returnValue(false));

        /** @var Temp $tmp */
        $tmp->file();
    }
}
