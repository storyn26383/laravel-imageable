<?php

namespace Tests;

use Mockery as m;
use CreateImagesTable;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $app = m::mock(Container::class);
        $app->shouldReceive('instance');
        $app->shouldReceive('offsetGet')->with('db')->andReturn(
            m::mock('db')->shouldReceive('connection')->andReturn(
                m::mock('connection')->shouldReceive('getSchemaBuilder')->andReturn('schema')->getMock()
            )->getMock()
        );
        $app->shouldReceive('offsetGet');

        Schema::setFacadeApplication($app);
        Schema::swap(Manager::schema());

        (new CreateImagesTable)->up();
    }

    public function tearDown()
    {
        (new CreateImagesTable)->down();

        m::close();

        Facade::clearResolvedInstances();

        parent::tearDown();
    }
}
