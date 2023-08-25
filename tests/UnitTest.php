<?php

namespace Tests;

use App\Main;
use Mockery as m;

class UnitTest extends TestCase
{
    public function testFoo()
    {
        $main = new Main;

        $foo = m::mock(\App\Foo::class);
        $foo->shouldReceive('foo')
            ->once()
            ->andReturn('bar');

        $this->assertEquals('bar', $main->foo($foo));
    }
}
