<?php

namespace Tests;

use App\Main;
use Mockery as m;
use Illuminate\Support\Facades\Cache;

class UnitTest extends TestCase
{
    public function testFoo()
    {
        $main = new Main;

        Cache::shouldReceive('get')->with('foo')->once()->andReturn('bar');

        $this->assertEquals('bar', $main->foo());
    }
}
