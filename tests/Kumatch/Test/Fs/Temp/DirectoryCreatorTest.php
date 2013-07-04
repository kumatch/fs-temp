<?php

namespace Kumatch\Test\Fs\Temp;

use Kumatch\Fs\Temp\DirectoryCreator;

class DirectoryCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $skelton;

    /** @var  string */
    protected $dir;
    /** @var  string */
    protected $tempPath;

    protected function setUp()
    {
        parent::setUp();

        $this->dir = sys_get_temp_dir();
        $this->tempPath = $this->dir . "/kumatch-test-fs-tmp-" . microtime(true);
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


    public function test一時ディレクトリを作成するとユニークな名称を持つディレクトリパスが用意される()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('_create'))
            ->getMock();
        $creator->expects($this->once())
            ->method('_create')
            ->will($this->returnValue(true));

        /** @var DirectoryCreator $creator */
        $dir = $creator->create();

        $this->assertRegExp(sprintf('!^%s/tmp\-[a-z0-9]+$!i', $this->dir), $dir);
    }

    public function test接頭辞を指定して一時ディレクトリを作成する()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('_create'))
            ->getMock();
        $creator->expects($this->once())
            ->method('_create')
            ->will($this->returnValue(true));

        /** @var DirectoryCreator $creator */
        $dir = $creator->prefix('foo_')->create();

        $this->assertRegExp(sprintf('!^%s/foo_[a-z0-9]+$!i', $this->dir), $dir);
    }

    public function test接尾辞を指定して一時ディレクトリを作成する()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('_create'))
            ->getMock();
        $creator->expects($this->once())
            ->method('_create')
            ->will($this->returnValue(true));

        /** @var DirectoryCreator $creator */
        $dir = $creator->suffix('.bar')->create();

        $this->assertRegExp(sprintf('!^%s/tmp-[a-z0-9]+\.bar$!i', $this->dir), $dir);
    }

    public function test接頭辞および接尾辞を指定して一時ディレクトリを作成する()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('_create'))
            ->getMock();
        $creator->expects($this->once())
            ->method('_create')
            ->will($this->returnValue(true));

        /** @var DirectoryCreator $creator */
        $dir = $creator->prefix('foo_')->suffix('.bar')->create();

        $this->assertRegExp(sprintf('!^%s/foo_[a-z0-9]+.bar$!i', $this->dir), $dir);
    }

    public function test一時ディレクトリを作成するとモード700で新規作成される()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $creator->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var DirectoryCreator $creator */
        $creator->create();

        $this->assertTrue(file_exists($this->tempPath));
        $this->assertTrue(is_dir($this->tempPath));

        $mode = substr(sprintf('%o', fileperms($this->tempPath)), -4);

        $this->assertEquals('0700', $mode);
    }

    public function test一時ディレクトリをモードを指定して作成する()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $creator->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue($this->tempPath));

        $this->assertFalse(file_exists($this->tempPath));

        /** @var DirectoryCreator $creator */
        $creator->mode(0755)->create();

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
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('createUniquePathName'))
            ->getMock();
        $creator->expects($this->once())
            ->method('createUniquePathName')
            ->will($this->returnValue(null));

        /** @var DirectoryCreator $creator */
        $creator->create();
    }

    /**
     * @expectedException \Kumatch\Fs\Temp\Exception\Exception
     */
    public function test一時ディレクトリの作成に失敗した場合は例外が発生()
    {
        $creator = $this->getMockBuilder('Kumatch\Fs\Temp\DirectoryCreator')
            ->setConstructorArgs(array($this->dir))
            ->setMethods(array('_create'))
            ->getMock();
        $creator->expects($this->once())
            ->method('_create')
            ->will($this->returnValue(false));

        /** @var DirectoryCreator $creator */
        $creator->create();
    }
}
