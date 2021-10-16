<?php

namespace XbNz\Resolver\Tests\Feature\Ip;

use XbNz\Resolver\Domain\Ip\Builders\DriverBuilder;
use XbNz\Resolver\Resolver\Resolver;
use function app;

class ResolverTest extends \XbNz\Resolver\Tests\TestCase
{
    /** @test */
    public function builder_is_successfully_instantiated()
    {
        $builder = app(Resolver::class)
            ->ip();

        $this->assertInstanceOf(DriverBuilder::class, $builder);


    }
}