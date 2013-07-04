<?php

namespace Kumatch\Test\Fs\Temp;

use Kumatch\Fs\Temp\Name;

class NameTest extends \PHPUnit_Framework_TestCase
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


    public function test適当な名前を生成する()
    {
        $this->assertRegExp('!^tmp\-[a-z0-9]+$!i', Name::create());
        $this->assertRegExp('!^first_[a-z0-9]+$!i', Name::create('first_'));
        $this->assertRegExp('!^tmp-[a-z0-9]+\.end$!', Name::create(null, ".end"));
        $this->assertRegExp('!^[0-9]+[a-z0-9]+\.end$!', Name::create("", '.end'));
        $this->assertRegExp('!^foo\-[a-z0-9]+\.bar$!', Name::create("foo-", ".bar"));
    }
}
